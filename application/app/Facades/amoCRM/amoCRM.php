<?php

namespace App\Facades\amoCRM;

use App\Models\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static getApi(Account $account)
 * @method static save(Model $first, $tokens)
 * @method static long()
 */
class amoCRM extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'amocrm';
    }
}
