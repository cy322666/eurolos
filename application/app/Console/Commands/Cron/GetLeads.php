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
                ->get()
                ->pluck('entity_id');

            foreach ($leadIds as $leadId) {

                try {
                    $lead = $this->client->leads()->getOne($leadId, [LeadModel::CONTACTS]);

                } catch (\AmoCRM\Exceptions\AmoCRMApiNoContentException $e) {

                    continue;
                }

                $fields = [];

                $cFields = $lead->getCustomFieldsValues()->toArray();

                foreach ($cFields as $cField) {

                    foreach (GetLeadStatuses::$fields as $fieldName => $fieldKey) {

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
                    'responsible_name' => Staff::query()
                        ->where('staff_id', $lead->getResponsibleUserId())
                        ->first()
                            ?->name,
                ]);

                Lead::query()->updateOrCreate(['lead_id' => $lead->getId()], $fields);
            }

        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {

//            dd($e->getFile().' '.$e->getLine(), $lead->toArray());

//            dd($e->);

            Log::error(json_encode($e->getLastRequestInfo()));

            throwException($e->getMessage() .' '. $e->getLastRequestInfo());
        }
    }
}
