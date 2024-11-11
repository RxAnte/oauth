<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;

readonly class CacheKey
{
    public static function get(Jwt $jwt): string
    {
        return 'auth0_user_info_response' . $jwt->cacheKey;
    }
}
