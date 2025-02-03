<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    use HasFactory;

    protected $table = 'events_lead_status';

    protected $fillable = [
        'event_id',
        'status_id_before',
        'status_id_after',
        'entity_id',
        'entity_type',
        'event_created_by',
        'event_created_at',
        'responsible_contact',
        'responsible_lead',
        'company_source',
        'channel_source',
        'category',
        'loss_reason',
        'returned_failure',
        'lead_class',
        'measured',
        'date_measured',
        'date_sale_op',
        'date_install',
        'event_created_date',
        'event_created_time',
        'measurer',
    ];
}
