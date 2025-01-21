<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $table = 'events_calls';

    protected $fillable = [
        'event_id',
        'type',
        'entity_id',
        'entity_type',
        'created_by',
        'call_created_at',
        'call_created_timestamp',
        'responsible_contact',
        'responsible_lead',
        'status_id',
    ];
}
