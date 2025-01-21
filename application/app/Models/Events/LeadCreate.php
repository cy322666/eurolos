<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCreate extends Model
{
    protected $table = 'events_lead_create';

    protected $fillable = [
        'event_id',
        'entity_id',
        'event_created_by',
        'event_created_at',
        'responsible_contact',
        'responsible_lead',
        'company_source',
        'channel_source',
        'category',
    ];
}
