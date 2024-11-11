<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;

interface CacheResponse
{
    public function cache(Jwt $jwt, Auth0Response $response): void;
}
