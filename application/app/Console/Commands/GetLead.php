<?php

namespace App\Console\Commands;

use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Models\LeadModel;
use App\Console\Commands\Cron\GetLeads;
use App\Facades\amoCRM\amoCRM;
use App\Models\Entities\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-lead {lead_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private $client;
//    private $leadId;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->client = amoCRM::long();

            $lead = $this->client->leads()->getOne($this->argument('lead_id'), [LeadModel::CONTACTS]);

            $fields = [];

            $cFields = $lead->getCustomFieldsValues()->toArray();

            foreach ($cFields as $cField) {
                foreach (GetLeads::$fields as $fieldName => $fieldKey) {
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
                'pipeline_id' => $lead->getPipelineId(),
//                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            Lead::query()
                ->where('lead_id', $lead->getId())
                ->update($fields);

        } catch (AmoCRMApiNoContentException $e) {

            Log::error(__METHOD__.' : '.$e->getLine(), [$e->getMessage()]);
        }
    }
}
