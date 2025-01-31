<?php

namespace App\Jobs\Events;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Models\LeadModel;
use App\Facades\amoCRM\amoCRM;
use App\Models\Entities\Staff;
use App\Models\Entities\Status;
use App\Models\Hooks\Talk;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TalkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private AmoCRMApiClient $client;

    /**
     * Create a new job instance.
     *
     * 9) Количество начатых переписок в мессенджерах. С фильтрацией по менеджерам.
     */
    public function __construct() {}

    /**
     * Execute the job.
     * @throws AmoCRMMissedTokenException
     */
    public function handle(Request $request): void
    {
        $this->client = amoCRM::long();

        $lead = $this->client->leads()->getOne((int)$request->talk['add'][0]['entity_id'], [LeadModel::CONTACTS]);

        $createdAt = Carbon::parse($lead->getCreatedAt());

        $talk = Talk::query()->create([
            'talk_id' => $request->talk['add'][0]['talk_id'],
            'talk_created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'rate' => $request->talk['add'][0]['rate'],
            'contact_id' => (int)$request->talk['add'][0]['contact_id'],
            'chat_id' => $request->talk['add'][0]['chat_id'],
            'entity_id' => (int)$request->talk['add'][0]['entity_id'],
            'entity_type' => $request->talk['add'][0]['entity_type'],
            'is_in_work' => $request->talk['add'][0]['is_in_work'],
            'is_read' => $request->talk['add'][0]['is_read'],
            'origin' => $request->talk['add'][0]['origin'],
            'body' => json_encode($request->talk['add'][0]),
            'lead_created_date' => $createdAt->format('Y-m-d'),
            'lead_created_time' => $createdAt->format('H:i:s'),
        ]);

        $contact = $this->client->contacts()->getOne($talk->contact_id);

        if ($talk->entity_type == 'lead') {

            $lead = $this->client->leads()->getOne($talk->entity_id);

            $talk->responsible_lead = $lead->getResponsibleUserId();
            $talk->status_id = $lead->getStatusId();
            $talk->responsible_name = Staff::query()
                ->where('staff_id', $lead->getResponsibleUserId())
                ->first()
                ?->name;
            $talk->status_name = Status::query()
                ->where('status_id', $lead->getStatusId())
                ->first()
                ?->status_name;
        }

        $talk->responsible_contact = $contact->getResponsibleUserId();
        $talk->save();
    }
}
