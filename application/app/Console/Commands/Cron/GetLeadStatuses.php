<?php

namespace App\Console\Commands\Cron;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\EventsFilter;
use AmoCRM\Models\EventModel;
use App\Facades\amoCRM\amoCRM;
use App\Models\Entities\Status;
use App\Models\Events\LeadCreate;
use App\Models\Events\LeadStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;

use Illuminate\Database\UniqueConstraintViolationException;

use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\throwException;

class GetLeadStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-lead-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    //1) Количество лидов, которые перевели в статус Целевой лид в день. С фильтрацией по менеджерам и источникам лидов.
    //2) Количество замеров назначенных в день. Перевод в статус Замер назначен. С возможностью фильтрации по менеджерам. также по источникам лидов
    //7) Количество успешных монтажей (Статус Успешно реализовано), за текущий отчетный период. Фильтрация по менеджерам, замерщикам и категории продажи. также по источникам лидов
    //4) Количество заключенных договоров на участке. Фильтрация по менеджерам и по замерщикам. также по источникам лидов
    //6) Количество монтажей с датой текущий отчетный период (месяц). С фильтрацией по менеджерам, замерщикам и категории продажи.
    //5) Общее количество заключенных договоров за день. Фильтрация по менеджерам и по замерщикам. также по источникам лидов.

    private AmoCRMApiClient $client;

    const
        MAIN_PIPELINE_ID = 55089,
        STATUS_SORT_AT = 90;

    static array $fields = [
        'Компания источник' => 'company_source',
        'Откуда пришел' => 'channel_source',
        'Причина отказа основная' => 'loss_reason',
        'Реанинирован из отказа' => 'returned_failure',
        'Классификация лида' => 'lead_class',
        'Замер выполнен' => 'measured',
        'Дата замера' => 'date_measured',
        'Дата NEW (монтаж)' => 'date_install',
        'ОП' => 'date_sale_op',
        'Замерщик' => 'measurer',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->client = amoCRM::long();

        $statuses = Status::query()
//            ->where('pipeline_id', self::MAIN_PIPELINE_ID)
//            ->where('archived', false)
//            ->where('status_sort', '>=', self::STATUS_SORT_AT)
            ->get()
            ->sortBy('status_sort')
            ->pluck('status_id')
            ->toArray();

        $filter = (new EventsFilter())
            ->setTypes(['lead_status_changed'])
            ->setValueAfter(Status::prepareStatusFilter($statuses))
            ->setLimit(250);

        try {
            $events = $this->client->events()->get($filter);

            /** @var EventModel $event */
            foreach ($events as $event) {

                try {

                    $leadStatus = LeadStatus::query()
                        ->where('event_id', $event->getId())
                        ->first();

                    if (!$leadStatus) {

                        $leadStatus = LeadStatus::query()
                            ->create([
                                    'event_id' => $event->getId(),
                                    'status_id_before' => $event->getValueBefore()[0]['lead_status']['id'],
                                    'status_id_after' => $event->getValueAfter()[0]['lead_status']['id'],
                                    'entity_id' => $event->getEntityId(),
                                    'entity_type' => $event->getEntityType() == 'lead' ? 2 : 1,
                                    'event_created_by' => $event->getCreatedBy(),
                                    'event_created_date' => Carbon::parse($event->getCreatedAt())->format('Y-m-d'),
                                    'event_created_time' => Carbon::parse($event->getCreatedAt())->format('H:i'),
                                    'event_created_at' => Carbon::parse($event->getCreatedAt())->format('Y-m-d H:i'),
                                ]
                            );

                        $lead = $this->client->leads()->getOne($event->getEntityId());

                        $leadStatus->responsible_lead = $lead->getResponsibleUserId();

                        $cFields = $lead->getCustomFieldsValues()->toArray();

                        foreach ($cFields as $cField) {

                            foreach (static::$fields as $fieldName => $fieldKey) {

                                if ($cField['field_name'] == $fieldName) {

                                    $leadStatus->{$fieldKey} = $cField['values'][0]['value'];
                                }
                            }
                        }
                        $leadStatus->save();
                    }

                } catch (UniqueConstraintViolationException) {}
            }

        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {

            Log::error(__METHOD__.' : '.$e->getLine(), [$e->getMessage(), $e->getLastRequestInfo()]);
        }
    }
}
