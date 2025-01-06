<?php

namespace App\Console\Commands;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Models\UserModel;
use App\Facades\amoCRM\amoCRM;
use App\Models\Entities\Staff;
use App\Models\Entities\Status;
use Carbon\Carbon;
use Illuminate\Console\Command;

use function PHPUnit\Framework\throwException;

class GetEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-entities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var AmoCRMApiClient
     */
    private AmoCRMApiClient $client;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->client = amoCRM::long();

        try {
            $pipelines = ($this->client->pipelines())->get();

            foreach ($pipelines as $pipeline) {

                $statuses = ($this->client->statuses($pipeline->getId()))->get();

                foreach ($statuses as $status) {

                    Status::query()->updateOrCreate([
                        'status_id'     => $status->getId(),
                    ], [
                        'status_name'   => $status->getName(),
                        'pipeline_id'   => $pipeline->getId(),
                        'archived'      => $pipeline->getIsArchive(),
                        'pipeline_name' => $pipeline->getName(),
                        'status_sort'   => $status->getSort(),
                    ]);
                }
            }

        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {

            throwException($e->getMessage() .' '. $e->getLastRequestInfo());
        }

        try {
            $users = ($this->client->users())->get();

            /** @var UserModel $user */
            foreach ($users as $user) {

                Staff::query()->updateOrCreate([
                    'staff_id' => $user->getId(),
                ], [
                    'name'     => $user->getName(),
                    'email'    => $user->getEmail(),
                    'is_admin' => $user->getRights()->getIsAdmin(),
                    'archived' => !$user->getRights()->getIsActive(),
                ]);
            }

        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {

            throwException($e->getMessage() .' '. $e->getLastRequestInfo());
        }
    }
}
