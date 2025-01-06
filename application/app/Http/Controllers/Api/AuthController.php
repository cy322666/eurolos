<?php

namespace App\Http\Controllers\Api;

use App\Facades\amoCRM\amoCRM;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function redirect(Request $request): void
    {
        $amoApi = amoCRM::getApi(Account::query()->first());

        $tokens = $amoApi
            ->setAccountBaseDomain(env('AMOCRM_SUBDOMAIN'))
            ->getOAuthClient()
            ->getAccessTokenByCode($request->code);

        amoCRM::save(Account::query()->first(), $tokens);
    }
}
