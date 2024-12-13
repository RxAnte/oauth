<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

readonly class FetchUserInfoForM2M implements FetchUserInfo
{
    public function fetch(Jwt $jwt): OauthUserInfo
    {
        return new OauthUserInfo(
            isValid: true,
            sub: $jwt->sub,
            email: $jwt->sub . '@m2m',
            name: $jwt->sub,
            givenName: $jwt->sub,
            familyName: $jwt->sub,
        );
    }
}
