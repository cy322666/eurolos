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

    public AmoCRMApiClient $client;
    public mixed $leadId;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
//        $this->leadId = $leadId;
        $this->client = amoCRM::long();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

    }
}
