<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Models\Token;
use App\Traits\TSingleton;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use League\OAuth2\Client\Token\AccessToken;
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

    public function saveToken($clientToken, $accountBaseDomain, $accessToken): void {
        $token = Token::query()->where('baseDomain', $accountBaseDomain)->first();

        $tokenArr = [
            'clientToken' => $clientToken,
            'baseDomain' => $accountBaseDomain,
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'expires' => date("Y-m-d H:m:s", $accessToken->getExpires())
        ];

        if (!$token) {
            $token = Token::query()->create($tokenArr);
            $token->save();
        } else {
            unset($tokenArr['clientToken']);
            unset($tokenArr['baseDomain']);
            $token->update($tokenArr);
        }
    }

    public function setAuthCookie($accountBaseDomain, $clientToken) {
        $token = Token::query()->where('baseDomain', $accountBaseDomain)->first();

        Cookie::queue('client_token', !$token ? $clientToken : $token->clientToken, 7 * 24 * 60); // Куки будут храниться 7 дней
    }

    public function setApiCredentials($accountBaseDomain, $accessToken): void {
        AmoCRM::instance()->getApiClient()->setAccountBaseDomain($accountBaseDomain)->setAccessToken($accessToken)
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    $this->saveToken(null, $baseDomain, $accessToken);
                });
    }

    public function getTokenFromCookie(): Model|Collection|Builder|array|null {
        if (Cookie::has('client_token')) {
            return Token::query()->find(Cookie::get('client_token'));
        }

        return null;
    }

    public function getAccessTokenFromCookie(Model $token): ?AccessToken {
        if ($token) {
            $tokenArr = [
                'access_token' => $token->accessToken,
                'refresh_token' => $token->refreshToken,
                'expires' => $token->expires
            ];

            return new AccessToken($tokenArr);
        }

        return null;
    }
}
