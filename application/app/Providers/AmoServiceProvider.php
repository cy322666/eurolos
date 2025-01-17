<?php

namespace App\Providers;

use App\Facades\amoCRM\amoCRMManager;
use App\Models\Account;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Client\Token\AccessToken;

class AmoServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot(): void
    {
        $this->app->singleton('amocrm', function($app) {

            $account = Account::query()->first();

            if ($account) {

                if ($account->access_token && $account->refresh_token && $account->expires_in) {

                    return new AmoCrmManager(new AccessToken([
                        'access_token'  => $account->access_token,
                        'refresh_token' => $account->refresh_token,
                        'expires'       => $account->expires_in,
                    ]));

                }
            } else {
                Account::query()->create([
                    'subdomain' => env('AMO_SUBDOMAIN'),
                    'name' => 'base',
                ]);
            }

            if (!empty(env('AMOCRM_LONG_TOKEN')))

                return new AmoCrmManager(new AccessToken([
                    'access_token' => env('AMOCRM_LONG_TOKEN'),
                ]));
            else
                throw new \Exception('Token is missing');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'amocrm'
        ];
    }
}
