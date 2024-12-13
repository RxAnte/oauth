<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;

readonly class CacheResponseNoOp implements CacheResponse
{
    public function cache(Jwt $jwt, FusionAuthResponse $response): void
    {
    }
}
