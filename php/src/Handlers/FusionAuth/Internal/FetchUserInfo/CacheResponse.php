<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
interface CacheResponse
{
    public function cache(Jwt $jwt, FusionAuthResponse $response): void;
}
