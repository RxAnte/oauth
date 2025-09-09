<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponse;

/** @codeCoverageIgnore */
readonly class CacheResponseNoOp implements CacheResponse
{
    public function cache(JwtToken $jwt, RxAnteResponse $response): void
    {
    }
}
