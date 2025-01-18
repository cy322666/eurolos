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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->client = amoCRM::long();
        //сохраняем в events и далее раскидываем по табличкам с нужной инфой

        //  55089
        //1 65767209
        //2 65767597
        //7 142
        //4 10948758???
        //6 14126335
        //5 10948758

        $statuses = Status::query()
            ->where('pipeline_id', self::MAIN_PIPELINE_ID)
            ->where('archived', false)
            ->where('status_sort', '>=', self::STATUS_SORT_AT)
            ->limit(2)
            ->get()
            ->sortBy('status_sort')
            ->pluck('status_id')
            ->toArray();

        $filter = (new EventsFilter())
            ->setTypes(['lead_status_changed'])
            ->setValueAfter(Status::prepareStatusFilter($statuses))
            ->setLimit(500);

        try {
            $events = $this->client->events()->get($filter);

            /** @var EventModel $event */
            foreach ($events as $event) {

                try {
                    LeadStatus::query()->create([
                        'event_id' => $event->id,
                        'status_id_before' => $event->getValueBefore()[0]['lead_status']['id'],
                        'status_id_after' => $event->getValueAfter()[0]['lead_status']['id'],
                        'entity_id' => $event->getEntityId(),
                        'entity_type' => $event->getEntityType() == 'lead' ? 2 : 1,
                        'event_created_by' => $event->getCreatedBy(),
                        'event_created_at' => Carbon::parse($event->getCreatedAt())->format('Y-m-d H:i:s'),
                    ]);
                } catch (UniqueConstraintViolationException) {

                    break;
                }
            }

        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {

            Log::error(json_encode($e->getLastRequestInfo()));

            throwException($e->getMessage() .' '. $e->getLastRequestInfo());
        }
    }
}
