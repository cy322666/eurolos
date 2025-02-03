<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    protected $fillable = [
        'lead_id',
        'contact_id',
        'responsible_lead',
        'category',
        'loss_reason',
        'company_source',
        'channel_source',
        'returned_failure',
        'lead_class',
        'measured',
        'date_measured',
        'lead_created_date',
        'lead_created_time',
        'date_sale_op',
        'date_install',
        'first_touch',
        'responsible_name',
        'status_id',
        'status_name',
        'doc',
    ];
}
