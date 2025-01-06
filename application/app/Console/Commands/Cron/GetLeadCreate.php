<?php

namespace App\Console\Commands\Cron;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\EventsFilter;
use AmoCRM\Models\EventModel;
use App\Facades\amoCRM\amoCRM;
use App\Models\Events\LeadCreate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\throwException;

class GetLeadCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-lead-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Новые заявки';
    private AmoCRMApiClient $client;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->client = amoCRM::long();

        $filter = (new EventsFilter())
            ->setTypes(['lead_added'])
            ->setLimit(500);

        try {
            $events = $this->client->events()->get($filter);

            /** @var EventModel $event */
            foreach ($events as $event) {

                try {
                    LeadCreate::query()->create([
                        'event_id' => $event->id,
                        'entity_id' => $event->getEntityId(),
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
