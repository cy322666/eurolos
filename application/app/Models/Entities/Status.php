<?php

namespace App\Models\Entities;

use App\Console\Commands\Cron\GetLeadStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    protected $fillable = [
        'status_id',
        'status_name',
        'pipeline_id',
        'pipeline_name',
        'archived',
        'deleted',
        'status_sort',
    ];

    public static function prepareStatusFilter(array $statuses) : array
    {
        $prepareStatuses = [];

        foreach ($statuses as $status) {

            $prepareStatuses['leads_statuses'][] = [
                'pipeline_id' => GetLeadStatuses::MAIN_PIPELINE_ID,
                'status_id'   => $status,
            ];

        }

        $prepareStatuses['leads_statuses'][] = [
            'pipeline_id' => GetLeadStatuses::MAIN_PIPELINE_ID,
            'status_id'   => 142,
        ];

        return $prepareStatuses;
    }
}
