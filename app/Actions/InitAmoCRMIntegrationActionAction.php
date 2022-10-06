<?php

namespace App\Actions;

use App\Contracts\InitAmoCRMIntegrationActionContract;
use App\Services\AmoCRM;
use App\Utilities\AmoCRMUtils;
use Illuminate\Http\Request;

class InitAmoCRMIntegrationActionAction implements InitAmoCRMIntegrationActionContract {

    public function __invoke(Request $request) {

        $token = AmoCRM::instance()->getTokenFromCookie();

        if ($token) {
            $accessToken = AmoCRM::instance()->getAccessTokenFromCookie($token);
            AmoCRM::instance()->setApiCredentials($token->baseDomain, $accessToken);

            return view('index', ['name' => $token->baseDomain]);
        }

        if (!$request->exists('code') || !$request->exists('referer')) {
            return AmoCRMUtils::generateIntegrationView();
        }

        $accountBaseDomain = $request->get('referer');
        $accessToken = AmoCRM::instance()->getAccessToken($accountBaseDomain, $request->get('code'));

        // TODO проверка получен ли токен

        $clientToken = bin2hex(random_bytes(24));

        AmoCRM::instance()->saveToken($clientToken, $accountBaseDomain, $accessToken);
        AmoCRM::instance()->setAuthCookie($accountBaseDomain, $clientToken);
        AmoCRM::instance()->setApiCredentials($accountBaseDomain, $accessToken);


        return view('index', ['name' => $accountBaseDomain]);
    }
}
