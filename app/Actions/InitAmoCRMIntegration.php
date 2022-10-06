<?php

namespace App\Actions;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Exceptions\BadTypeException;
use App\Contracts\InitAmoCRMIntegrationContract;
use App\Services\AmoCRM;
use Illuminate\Http\Request;

class InitAmoCRMIntegration implements InitAmoCRMIntegrationContract {

    public function __invoke(Request $request) {

        $token = AmoCRM::instance()->getTokenFromCookie();

        if ($token) {
            $accessToken = AmoCRM::instance()->getAccessTokenFromCookie($token);
            AmoCRM::instance()->setApiCredentials($token->baseDomain, $accessToken);

            try {
                $leadName = AmoCRM::instance()->getApiClient()->leads()->get()->first()->getName();
                return view('welcome', ['integrationButton' => '', 'request' => $leadName]);
            } catch (AmoCRMApiException|AmoCRMMissedTokenException|AmoCRMoAuthApiException $ex) {
                return view('welcome', ['integrationButton' => '', 'request' => $ex->getMessage()]);
            }
        }

        if (!$request->exists('code') || !$request->exists('referer')) {
            try {
                // Генерируем кнопку интеграции
                $button = AmoCRM::instance()->getApiClient()->getOAuthClient()->getOAuthButton(
                    [
                        'title' => 'Установить интеграцию',
                        'compact' => false,
                        'class_name' => 'className',
                        'color' => 'default',
                        'error_callback' => 'handleOauthError',
                        'state' => bin2hex(random_bytes(16)),
                        'mode' => 'popup'
                    ]
                );

                return view('welcome', ['integrationButton' => $button, 'request' => '']);
            } catch (BadTypeException $ex) {
            }
        } else {
            $accountBaseDomain = $request->get('referer');
            $accessToken = AmoCRM::instance()->getAccessToken($accountBaseDomain, $request->get('code'));

            // TODO проверка получен ли токен

            $clientToken = bin2hex(random_bytes(24));

            AmoCRM::instance()->saveToken($clientToken, $accountBaseDomain, $accessToken);
            AmoCRM::instance()->setAuthCookie($accountBaseDomain, $clientToken);
            AmoCRM::instance()->setApiCredentials($accountBaseDomain, $accessToken);

            return view('welcome', ['integrationButton' => '', 'request' => AmoCRM::instance()->getApiClient()->leads()->get()->first()->getName()]);
        }
    }
}
