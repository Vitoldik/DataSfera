<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Models\Token;
use App\Traits\TSingleton;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AmoCRM {

    use TSingleton;
    private AmoCRMApiClient $apiClient;

    protected function __construct() {
        $this->apiClient = new AmoCRMApiClient(config('amocrm.client_id'), config('amocrm.client_secret'), config('amocrm.redirect_uri'));
    }

    /**
     * @return AmoCRMApiClient
     */
    public function getApiClient(): AmoCRMApiClient {
        return $this->apiClient;
    }

    public function getAccessToken($accountBaseDomain, $code): ?AccessTokenInterface {
        try {
            $oauth = $this->getApiClient()->getOAuthClient();
            $oauth->setBaseDomain($accountBaseDomain);

            return $oauth->getAccessTokenByCode($code);
        } catch (AmoCRMoAuthApiException $ex) {
            return null;
        }
    }

    public function saveToken($accountBaseDomain, $accessToken): void {
        $token = Token::query()->find($accountBaseDomain);

        $tokenArr = [
            'baseDomain' => $accountBaseDomain,
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'expires' => date("Y-m-d H:m:s", $accessToken->getExpires())
        ];

        if (!$token) {
            $token = Token::query()->create($tokenArr);
            $token->save();
        } else {
            unset($tokenArr['baseDomain']);
            $token->update($tokenArr);
        }
    }
}
