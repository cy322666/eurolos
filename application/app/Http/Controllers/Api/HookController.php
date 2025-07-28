<?php

namespace App\Http\Controllers\Api;

use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Models\LeadModel;
use App\Console\Commands\Cron\GetLeads;
use App\Http\Controllers\Controller;
use App\Jobs\Events\TalkJob;
use App\Models\Entities\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HookController extends Controller
{
    public function talks(Request $request): void
    {
        TalkJob::dispatch($request);
    }

    public function leads(Request $request): void
    {
        try {

            $leadId = $request->leads['update'][0]['id'];

            $lead = $this->client->leads()->getOne($leadId, [LeadModel::CONTACTS]);

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
