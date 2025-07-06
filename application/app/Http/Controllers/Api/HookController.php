<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\Events\TalkJob;
use App\Models\Entities\Lead;
use Illuminate\Http\Request;

class HookController extends Controller
{
    public function talks(Request $request): void
    {
        TalkJob::dispatch($request);
    }

    public function leads(Request $request): void
    {
        Lead::query()->firstOrCreate(['lead_id' => $request->leads['update'][0]['id']]);
    }
}
