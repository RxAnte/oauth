<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class CacheKey
{
    public static function get(Jwt $jwt): string
    {
        return 'fusion_auth_user_info_response' . $jwt->cacheKey;
    }
}
