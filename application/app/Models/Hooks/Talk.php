<?php

namespace App\Models\Hooks;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talk extends Model
{
    use HasFactory;

    protected $table = 'hooks_talks';

    protected $fillable = [
        'talk_id',
        'talk_created_at',
        'rate',
        'contact_id',
        'chat_id',
        'entity_id',
        'entity_type',
        'is_in_work',
        'is_read',
        'origin',
        'body',
    ];
}
