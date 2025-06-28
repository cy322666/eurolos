<?php

namespace App\Console\Commands\Cron;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Filters\LeadsFilter;
use App\Facades\amoCRM\amoCRM;
use App\Models\Events\LeadCreate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetUpdatedLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-updated-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private AmoCRMApiClient $client;

    public static array $statuses = [
        142,
        143,
    ];

    public function handle()
    {
        $this->client = amoCRM::long();

        static::getLeads(1, $this->client);
        static::getLeads(2, $this->client);
        static::getLeads(3, $this->client);

        static::getLeadsUpdated(1, $this->client);
        static::getLeadsUpdated(2, $this->client);
        static::getLeadsUpdated(3, $this->client);

        static::getLeadsClosed(1, $this->client);
        static::getLeadsClosed(2, $this->client);
        static::getLeadsClosed(3, $this->client);
    }

    public static function getLeads(int $page, $client)
    {
        Log::debug(__METHOD__);

        $filter = (new LeadsFilter());
        $filter->setPipelineIds(GetLeadStatuses::MAIN_PIPELINE_ID);
        $filter->setLimit(250);
        $filter->setOrder('updated_at', 'desc');
        $filter->setPage($page);

        foreach (static::$statuses as $statusId) {

            $filter->setStatuses([[
                'status_id'   => $statusId,
                'pipeline_id' => GetLeadStatuses::MAIN_PIPELINE_ID,
            ]]);

            try {

                $leads = $client->leads()->get($filter);

            } catch (AmoCRMApiNoContentException $e) {

                Log::error(__METHOD__ . ' ' . $e->getLine().' '.$e->getMessage());

                continue;
            }

            foreach ($leads as $lead) {

                LeadCreate::query()->firstOrCreate(
                    ['entity_id' => $lead->getId()],
                    [
                        'event_id' => rand(1, 99999999999999999),
                        'entity_id' => $lead->getId(),
                        'event_created_by' => $lead->getCreatedBy(),
                        'event_created_at' => Carbon::parse($lead->getCreatedAt())->format('Y-m-d H:i:s'),
                    ]
                );
            }
        }
    }

    public static function getLeadsUpdated(int $page, $client)
    {
        Log::debug(__METHOD__);

        $filter = (new LeadsFilter());
        $filter->setPipelineIds(GetLeadStatuses::MAIN_PIPELINE_ID);
        $filter->setLimit(250);
        $filter->setUpdatedAt(Carbon::now()->subDays(2)->timestamp);
        $filter->setPage($page);

        try {

            $leads = $client->leads()->get($filter);

            foreach ($leads as $lead) {

                LeadCreate::query()->firstOrCreate(
                    ['entity_id' => $lead->getId()],
                    [
                        'event_id' => rand(1, 99999999999999999),
                        'entity_id' => $lead->getId(),
                        'event_created_by' => $lead->getCreatedBy(),
                        'event_created_at' => Carbon::parse($lead->getCreatedAt())->format('Y-m-d H:i:s'),
                    ]
                );
            }

        } catch (AmoCRMApiNoContentException $e) {

            Log::error(__METHOD__ . ' ' . $e->getLine().' '.$e->getMessage());
        }
    }

    public static function getLeadsClosed(int $page, $client)
    {
        Log::debug(__METHOD__);

        $filter = (new LeadsFilter());
        $filter->setPipelineIds(GetLeadStatuses::MAIN_PIPELINE_ID);
        $filter->setLimit(250);
        $filter->setClosedAt(Carbon::now()->subDays(2)->timestamp);

        try {

            $leads = $client->leads()->get($filter);

            foreach ($leads as $lead) {

                LeadCreate::query()->firstOrCreate(
                    ['entity_id' => $lead->getId()],
                    [
                        'event_id' => rand(1, 99999999999999999),
                        'entity_id' => $lead->getId(),
                        'event_created_by' => $lead->getCreatedBy(),
                        'event_created_at' => Carbon::parse($lead->getCreatedAt())->format('Y-m-d H:i:s'),
                    ]
                );
            }

        } catch (AmoCRMApiNoContentException $e) {

            Log::error(__METHOD__ . ' ' . $e->getLine().' '.$e->getMessage());
        }
    }
}
