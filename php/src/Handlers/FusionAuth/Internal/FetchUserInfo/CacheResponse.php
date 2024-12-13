<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;

interface CacheResponse
{
    public function cache(Jwt $jwt, FusionAuthResponse $response): void;
}
