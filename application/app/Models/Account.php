<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $access_token
 * @property mixed refresh_token
 * @property mixed expires_in
 */
class Account extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'account_id',
        'name',
        'code',
        'state',
        'subdomain',
        'client_id',
        'work',
        'client_secret',
        'referer',
        'expires_in',
        'created_at',
        'token_type',
        'redirect_uri',
        'endpoint',
        'expires_tariff',
        'redirect_uri',
    ];

    protected $guarded = [];
}
