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
        'responsible_contact',
        'responsible_lead',
        'status_id',
        'responsible_name',
        'status_name',
        'lead_created_date',
        'lead_created_time',
    ];
}
