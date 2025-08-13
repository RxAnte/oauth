<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse;

use Lcobucci\JWT\UnencryptedToken as JwtToken;

use function md5;

readonly class CacheKey
{
    public static function get(JwtToken $jwt): string
    {
        return 'rxante_auth_user_info_response' . md5($jwt->toString());
    }
}
