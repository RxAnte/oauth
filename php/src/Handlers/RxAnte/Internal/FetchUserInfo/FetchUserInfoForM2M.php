<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

use function is_string;

readonly class FetchUserInfoForM2M implements FetchUserInfo
{
    public function fetch(JwtToken $jwt): OauthUserInfo
    {
        $sub = $jwt->claims()->get('sub', 'm2m');
        $sub = is_string($sub) ? $sub : 'm2m';

        return new OauthUserInfo(
            isValid: true,
            sub: $sub,
            email: $sub . '@m2m',
            name: $sub,
            givenName: $sub,
            familyName: $sub,
        );
    }
}
