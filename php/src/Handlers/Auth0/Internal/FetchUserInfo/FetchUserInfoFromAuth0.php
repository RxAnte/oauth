<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

use function is_array;
use function json_decode;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class FetchUserInfoFromAuth0 implements FetchUserInfo
{
    public function __construct(
        private CacheResponseFactory $cacheResponseFactory,
        private GetAuth0ResponseFactory $getAuth0ResponseFactory,
    ) {
    }

    public function fetch(Jwt $jwt): OauthUserInfo
    {
        $response = $this->getAuth0ResponseFactory
            ->create($jwt)
            ->get($jwt);

        $this->cacheResponseFactory->create($response)->cache(
            $jwt,
            $response,
        );

        if ($response->isNotValid()) {
            return new OauthUserInfo();
        }

        $userInfo = json_decode($response->body, true);

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
