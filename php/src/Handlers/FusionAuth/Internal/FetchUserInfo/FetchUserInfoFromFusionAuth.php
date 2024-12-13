<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

use function is_array;
use function json_decode;
use function trim;

readonly class FetchUserInfoFromFusionAuth implements FetchUserInfo
{
    public function __construct(
        private CacheResponseFactory $cacheResponseFactory,
        private GetFusionAuthResponseFactory $getFusionAuthResponseFactory,
    ) {
    }

    public function fetch(Jwt $jwt): OauthUserInfo
    {
        $response = $this->getFusionAuthResponseFactory
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

        $givenName = $userInfo['given_name'] ?? '';

        $familyName = $userInfo['family_name'] ?? '';

        $name = $userInfo['name'] ?? '';

        if ($name === '') {
            $name = trim($givenName . ' ' . $familyName);
        }

        return new OauthUserInfo(
            $sub !== '',
            $sub,
            $userInfo['email'] ?? '',
            $name,
            $givenName,
            $familyName,
            $userInfo['picture'] ?? '',
        );
    }
}
