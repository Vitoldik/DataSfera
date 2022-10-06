<?php

namespace App\Utilities;

use AmoCRM\Exceptions\BadTypeException;
use App\Services\AmoCRM;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class AmoCRMUtils {

    public static function generateIntegrationView(): Factory|View|Application {
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

            return view('integrate', ['integrationButton' => $button]);
        } catch (BadTypeException $ex) {
            return view('integrate', ['integrationButton' => $ex->getMessage()]);
        }
    }

}
