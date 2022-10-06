<?php

namespace App\Actions;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Contracts\GetLeadActionContract;
use App\Services\AmoCRM;
use Illuminate\Http\Request;

class GetLeadActionAction implements GetLeadActionContract {

    public function __invoke(Request $request) {

        try {
            $token = AmoCRM::instance()->getTokenFromEncryptedCookie();

            if ($token) {
                $accessToken = AmoCRM::instance()->getAccessTokenFromCookie($token);
                AmoCRM::instance()->setApiCredentials($token->baseDomain, $accessToken);

                $leadName = AmoCRM::instance()->getApiClient()->leads()->get()->first()->getName();
            }

            return view('index', ['name' => $leadName]);
        } catch (AmoCRMApiException|AmoCRMMissedTokenException|AmoCRMoAuthApiException $ex) {
            return view('index', [ 'name' => $ex->getMessage()]);
        }
    }
}
