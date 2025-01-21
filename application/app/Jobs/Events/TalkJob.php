<?php

namespace App\Jobs\Events;

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

    /**
     * Create a new job instance.
     *
     * 9) Количество начатых переписок в мессенджерах. С фильтрацией по менеджерам.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(Request $request): void
    {
        Talk::query()->create([
            'talk_id' => $request->talk_id,
            'talk_created_at' => Carbon::parse($request->created_at)->format('Y-m-d H:i:s'),
            'rate' => $request->rate,
            'contact_id' => (int)$request->contact_id,
            'chat_id' => $request->chat_id,
            'entity_id' => (int)$request->entity_id,
            'entity_type' => $request->entity_type,
            'is_in_work' => $request->is_in_work,
            'is_read' => $request->is_read,
            'origin' => $request->origin,
            'body' => $request->json(),
        ]);
    }
}
