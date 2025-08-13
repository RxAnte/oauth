<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheResponseFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponseFactory;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

use function is_array;
use function json_decode;

readonly class FetchUserInfoFromRxAnteAuth implements FetchUserInfo
{
    public function __construct(
        private CacheResponseFactory $cacheResponseFactory,
        private GetRxAnteResponseFactory $getResponseFactory,
    ) {
    }

    public function fetch(JwtToken $jwt): OauthUserInfo
    {
        $responseWrapper = $this->getResponseFactory
            ->create($jwt)
            ->get($jwt);

        $this->cacheResponseFactory->create($responseWrapper)
            ->cache($jwt, $responseWrapper->response);

        if ($responseWrapper->response->isNotValid()) {
            return new OauthUserInfo();
        }

        $userInfo = json_decode(
            $responseWrapper->response->body,
            true,
        );

        /**
         * Defensive coding. If we don't have valid json, json_decode will not
         * return an array, and we'll get exceptions below
         */
        $userInfo = is_array($userInfo) ? $userInfo : [];

        $sub = (string) ($userInfo['sub'] ?? '');

        $roles = $userInfo['roles'] ?? [];

        if (! is_array($roles)) {
            $roles = [];
        }

        return new OauthUserInfo(
            $sub !== '',
            $sub,
            $userInfo['email'] ?? '',
            $userInfo['name'] ?? '',
            $userInfo['given_name'] ?? '',
            $userInfo['family_name'] ?? '',
            $userInfo['picture'] ?? '',
            $roles,
        );
    }
}
