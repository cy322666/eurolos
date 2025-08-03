<?php

namespace App\Jobs;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Models\LeadModel;
use App\Console\Commands\Cron\GetLeads;
use App\Facades\amoCRM\amoCRM;
use App\Models\Entities\Lead;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GetLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private AmoCRMApiClient $client;
    private mixed $leadId;

    /**
     * Create a new job instance.
     */
    public function __construct($leadId)
    {
        $this->leadId = $leadId;
        $this->client = amoCRM::long();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $lead = $this->client->leads()->getOne($this->leadId, [LeadModel::CONTACTS]);

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
