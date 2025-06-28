<?php

namespace App\Console\Commands\Cron;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\BaseRangeFilter;
use AmoCRM\Filters\EventsFilter;
use AmoCRM\Filters\Interfaces\HasOrderInterface;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\EventModel;
use App\Facades\amoCRM\amoCRM;
use App\Models\Entities\Lead;
use App\Models\Events\LeadCreate;
use App\Models\Events\LeadStatus;
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

    public static array $statuses = [
            24707860,
            65767209,
            9950760,
            65767141,
            71138757,
            10071768,
            65767145,
            65767149,
            9996006,
            65767209,
            68781613,
            26705880,
            65879153,
            9958824,
            9959097,
            33324984,
            72605217,
            9958827,
            65767597,
            33698169,
            65767573,
            11008437,
            65767669,
            10948758,
            14126338,
            14126335,
            65767725,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->client = amoCRM::long();

        $filter = (new LeadsFilter());
        $filter->setPipelineIds(GetLeadStatuses::MAIN_PIPELINE_ID);
        $filter->setLimit(500);
        $filter->setOrder('updated_at', 'desc');

        foreach (static::$statuses as $statusId) {

            $filter->setStatuses([[
                'status_id'   => $statusId,
                'pipeline_id' => GetLeadStatuses::MAIN_PIPELINE_ID,
            ]]);

            try {

                $leads = $this->client->leads()->get($filter);

            } catch (AmoCRMApiNoContentException $e) {

                Log::error(__METHOD__ . ' ' . $e->getLine().' '.$e->getMessage());

                continue;
            }

            foreach ($leads as $lead) {

                Lead::query()->firstOrCreate(['lead_id' => $lead->getId()]);
            }
        }

        $rangeFilter = new BaseRangeFilter();
        $rangeFilter->setFrom(Carbon::create(2025, 06, 01)->timestamp);
        $rangeFilter->setTo(Carbon::now()->timestamp);

        try {

            for ($page = 1; ; $page++) {

                $filter = (new LeadsFilter());
                $filter->setPipelineIds(GetLeadStatuses::MAIN_PIPELINE_ID);
                $filter->setLimit(500);
                $filter->setPage($page);
                $filter->setClosedAt($rangeFilter);

                $leads = $this->client->leads()->get($filter);

                if ($leads->isEmpty()) {

                    return;
                }

                foreach ($leads as $lead) {

//                    dump($lead->getId());

                    Lead::query()->firstOrCreate(['lead_id' => $lead->getId()]);
                }
            }

        } catch (AmoCRMApiNoContentException $e) {

            Log::error(__METHOD__.' : '.$e->getLine(), [$e->getMessage()]);
        }
    }
}
