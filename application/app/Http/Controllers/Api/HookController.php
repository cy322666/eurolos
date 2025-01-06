<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\Events\TalkJob;
use Illuminate\Http\Request;

class HookController extends Controller
{
    public function talks(Request $request): void
    {
        TalkJob::dispatch($request);
    }
}
