<?php

namespace App\Console\Commands\Cron;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Filters\EventsFilter;
use AmoCRM\Models\EventModel;
use App\Facades\amoCRM\amoCRM;
use App\Models\Account;
use App\Models\Entities\Call;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;

use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\throwException;

class GetCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-calls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '8) Количество совершенных звонков менеджеров (сумма, без разделения на входящие и исходящие). С фильтрацией по менеджерам';
    private AmoCRMApiClient $client;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->client = amoCRM::long();

        $filter = (new EventsFilter())
            ->setTypes(['incoming_call'])
            ->setLimit(500);

        try {

            $calls = $this->client->events()->get($filter);

            /** @var EventModel $call */
            foreach ($calls as $call) {

                try {
                    Call::query()->create([
                        'event_id' => $call->getId(),
                        'type' => $call->getType(),
                        'entity_id' => $call->getEntityId(),
                        'entity_type' => $call->getEntityType() == 'contact' ? 1 : 2,
                        'created_by' => $call->getCreatedBy(),
                        'call_created_at' => Carbon::parse($call->getCreatedAt())->format('Y-m-d H:i:s'),
                        'call_created_timestamp' => (int)$call->getCreatedAt(),
                    ]);

                } catch (UniqueConstraintViolationException) {

                    break;
                }
            }

        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {

            Log::error(json_encode($e->getLastRequestInfo()));

            throwException($e->getMessage() .' '. $e->getLastRequestInfo());
        }

        $filter = (new EventsFilter())
            ->setTypes(['outgoing_call'])
            ->setLimit(500);

        try {

            $calls = $this->client->events()->get($filter);

            /** @var EventModel $call */
            foreach ($calls as $call) {

                try {
                    Call::query()->create([
                        'event_id' => $call->getId(),
                        'type' => $call->getType(),
                        'entity_id' => $call->getEntityId(),
                        'entity_type' => $call->getEntityType() == 'contact' ? 1 : 2,
                        'created_by' => $call->getCreatedBy(),
                        'call_created_at' => Carbon::parse($call->getCreatedAt())->format('Y-m-d H:i:s'),
                        'call_created_timestamp' => (int)$call->getCreatedAt(),
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
