<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponse;

interface CacheResponse
{
    public function cache(JwtToken $jwt, RxAnteResponse $response): void;
}
