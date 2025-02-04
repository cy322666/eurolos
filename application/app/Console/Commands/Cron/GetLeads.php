<?php

namespace App\Console\Commands\Cron;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
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
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->client = amoCRM::long();

//            $filter = (new LeadsFilter())
//                ->setPipelineIds(GetLeadStatuses::MAIN_PIPELINE_ID)
//                ->setCreatedAt(Carbon::parse('01-01-2025')->timestamp)
//                ->setLimit(20);
//
//            $leads = $this->client->leads()->get($filter);

            $leadIds = LeadCreate::query()
                ->where('responsible_lead', null)
                ->get()
                ->pluck('entity_id')
                ->sortDesc('id');

            foreach ($leadIds as $leadId) {

                try {
                    $lead = $this->client->leads()->getOne($leadId, [LeadModel::CONTACTS]);
                } catch (\AmoCRM\Exceptions\AmoCRMApiNoContentException $e) {
                    LeadCreate::query()
                        ->where('entity_id', $leadId)
                        ->first()
                        ->update(['responsible_lead' => 'closed']);

                    sleep(2);

                    continue;
                }

                $fields = [];

                $cFields = $lead->getCustomFieldsValues()->toArray();

                foreach ($cFields as $cField) {
                    foreach (static::$fields as $fieldName => $fieldKey) {
                        if ($cField['field_name'] == $fieldName) {
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
//                    'responsible_name' => Staff::query()
//                        ->where('staff_id', $lead->getResponsibleUserId())
//                        ->first()
//                            ?->name,
//                    'status_name' => Status::query()
//                        ->where('status_id', $lead->getStatusId())
//                        ->first()
//                            ?->status_name,
                ]);

                Lead::query()->updateOrCreate(['lead_id' => $lead->getId()], $fields);

                if ($lead->getStatusId() == 143) {
                    LeadCreate::query()
                        ->where('entity_id', $lead->getId())
                        ->first()
                        ->update(['responsible_lead' => 'closed']);

                    sleep(2);
                }
            }

        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {

//            dd($e->getFile().' '.$e->getLine(), $lead->toArray());

//            dd($e->);

//            Log::error(json_encode($e->getLastRequestInfo()));

            dump($e->getMessage());
        }
    }
}
