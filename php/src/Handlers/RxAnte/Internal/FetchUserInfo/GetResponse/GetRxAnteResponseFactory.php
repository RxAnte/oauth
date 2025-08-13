<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Psr\Cache\CacheItemPoolInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheKey;

readonly class GetRxAnteResponseFactory
{
    public function __construct(
        private CacheItemPoolInterface $cachePool,
        private GetRxAnteResponseFromRxAnte $fromRxAnte,
        private GetRxAnteResponseFromSystemCache $fromCache,
    ) {
    }

    public function create(JwtToken $jwt): GetRxAnteResponse
    {
        if ($this->cachePool->hasItem(CacheKey::get($jwt))) {
            return $this->fromCache;
        }

        return $this->fromRxAnte;
    }
}
