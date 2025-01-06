<?php

namespace App\Facades\amoCRM;

use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Exceptions\InvalidArgumentException;
use App\Models\Account;
use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class amoCRMManager
{
    protected $access_token;

    protected AmoCRMApiClient $client;

    /**
     * Create a new manager instance.
     *
     * @param AccessTokenInterface $access_token
     */
    public function __construct(AccessTokenInterface $access_token)
    {
        $this->access_token = $access_token;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * Get the AmoCRM client instance.
     *
     * @param Account $account
     * @return AmoCRMApiClient
     */
    public function getApi(Account $account): AmoCRMApiClient
    {
        return (new AmoCRMApiClient(
            env('AMOCRM_CLIENT_ID'),
            env('AMOCRM_SECRET'),
            route('amocrm_redirect'))
        );
    }

    public function update()
    {
        $this->client = (new AmoCRMApiClient(
            env('AMOCRM_CLIENT_ID'),
            env('AMOCRM_SECRET'),
            route('amocrm_redirect'))
        )
            ->setAccessToken($this->access_token)
            ->setAccountBaseDomain(env('AMOCRM_DOMAIN'))
            ->onAccessTokenRefresh(
                callback: function(AccessTokenInterface $access_token, Account $account) {

                    $account->access_token = $access_token->getToken();
                    $account->refresh_token = $access_token->getRefreshToken();
                    $account->expires_in = $access_token->getExpires();
                    $account->save();
                });

        return $this->client;
    }

    public function save(Account $account, AccessTokenInterface $tokens)
    {
        $account->access_token  = $tokens->getToken();
        $account->refresh_token = $tokens->getRefreshToken();
        $account->expires_in    = $tokens->getExpires();
        $account->save();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function long()
    {
        $longLivedAccessToken = new LongLivedAccessToken(
            new AccessToken([
                'access_token' => env ('AMOCRM_LONG_TOKEN'),
            ])
        );

        return (new AmoCRMApiClient())->setAccessToken($longLivedAccessToken)
            ->setAccountBaseDomain(env('AMOCRM_SUBDOMAIN'));
    }
}
