<?php

namespace App\Actions;

use AmoCRM\Exceptions\BadTypeException;
use App\Contracts\InitAmoCRMIntegrationContract;
use App\Services\AmoCRM;
use Illuminate\Http\Request;

class InitAmoCRMIntegration implements InitAmoCRMIntegrationContract {

    public function __invoke(Request $request) {
        if (!$request->exists('code') || !$request->exists('referer')) {
            $state = bin2hex(random_bytes(16));

            try {
                // Генерируем кнопку интеграции
                $button = AmoCRM::instance()->getApiClient()->getOAuthClient()->getOAuthButton(
                    [
                        'title' => 'Установить интеграцию',
                        'compact' => true,
                        'class_name' => 'className',
                        'color' => 'default',
                        'error_callback' => 'handleOauthError',
                        'state' => $state,
                    ]
                );

                return view('welcome', ['integrationButton' => $button, 'request' => '']);
            } catch (BadTypeException $ex) {
            }
        } else {
            $accountBaseDomain = $request->get('referer');
            $accessToken = AmoCRM::instance()->getAccessToken($accountBaseDomain, $request->get('code'));

            // TODO проверка получен ли токен

            AmoCRM::instance()->saveToken($accountBaseDomain, $accessToken);

            return view('welcome', ['integrationButton' => '', 'request' => $accessToken->getToken()]);
        }
    }
}
