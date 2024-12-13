<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use Psr\Cache\CacheItemPoolInterface;
use RxAnte\OAuth\UserInfo\Jwt;

readonly class GetFusionAuthResponseFactory
{
    public function __construct(
        private CacheItemPoolInterface $cachePool,
        private GetFusionAuthResponseFromSystemCache $fromCache,
        private GetFusionAuthResponseFromFusionAuth $fromFusionAuth,
    ) {
    }

    public function create(Jwt $jwt): GetFusionAuthResponse
    {
        if ($this->cachePool->hasItem(CacheKey::get($jwt))) {
            return $this->fromCache;
        }

        return $this->fromFusionAuth;
    }
}
