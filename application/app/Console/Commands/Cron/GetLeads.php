<?php

namespace App\Console\Commands\Cron;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Facades\amoCRM\amoCRM;
use App\Models\Entities\Lead;
use App\Models\Entities\Staff;
use App\Models\Entities\Status;
use App\Models\Events\LeadCreate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\throwException;

class GetLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-leads';

    private AmoCRMApiClient $client;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        'Первое касание' => 'first_touch',
        'Отправлено на DOC' => 'doc',
        'Замерщик' => 'measurer',
        'Категория продажи' => 'category',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->client = amoCRM::long();

            $leadIds = LeadCreate::query()
                ->where('responsible_lead', null)
                ->get()
                ->pluck('entity_id')
                ->sortBy('updated_at');

            foreach ($leadIds as $leadId) {

                try {
                    $lead = $this->client->leads()->getOne($leadId, [LeadModel::CONTACTS]);

                } catch (AmoCRMApiNoContentException $e) {

                    LeadCreate::query()
                        ->where('entity_id', $leadId)
                        ->first()
                        ->delete();

                    continue;
                }

                $fields = [];

                $cFields = $lead->getCustomFieldsValues()->toArray();

                foreach ($cFields as $cField) {
                    foreach (static::$fields as $fieldName => $fieldKey) {
                        if ($cField['field_name'] == $fieldName) {

                            if ($cField['field_type'] == 'date') {

                                $fields[$fieldKey] = $cField['values'][0]['value']
                                    ->timezone('Europe/Moscow')
                                    ->format('Y-m-d');

                            } else
                                $fields[$fieldKey] = $cField['values'][0]['value'];
                        }
                    }
                }

                $createdAt = Carbon::parse($lead->getCreatedAt());

                $fields = array_merge($fields, [
                    'lead_created_date' => $createdAt->format('Y-m-d'),
                    'lead_created_time' => $createdAt->format('H:i:s'),
                    'contact_id' => $lead->getContacts()?->first()?->id,
                    'responsible_lead' => $lead->getResponsibleUserId(),
                    'status_id' => $lead->getStatusId(),
                ]);

                Lead::query()->updateOrCreate(['lead_id' => $lead->getId()], $fields);
            }

        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {

            Log::error(__METHOD__, [json_encode($e->getLastRequestInfo())]);
        }
    }
}
