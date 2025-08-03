<?php

namespace App\Http\Controllers\Api;

use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Models\LeadModel;
use App\Console\Commands\Cron\GetLeads;
use App\Http\Controllers\Controller;
use App\Jobs\Events\TalkJob;
use App\Jobs\GetLead;
use App\Models\Entities\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class HookController extends Controller
{
    public function talks(Request $request): void
    {
        TalkJob::dispatch($request);
    }

    public function leads(Request $request): void
    {
        $leadId = (int)$request->all()['leads']['update'][0]['id'];

        Artisan::call('app:get-lead', ['lead_id' => $leadId]);
    }
}
