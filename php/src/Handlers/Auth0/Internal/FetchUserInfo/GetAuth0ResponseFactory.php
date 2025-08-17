<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use Psr\Cache\CacheItemPoolInterface;
use RxAnte\OAuth\UserInfo\Jwt;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class GetAuth0ResponseFactory
{
    public function __construct(
        private CacheItemPoolInterface $cachePool,
        private GetAuth0ResponseFromAuth0 $fromAuth0,
        private GetAuth0ResponseFromSystemCache $fromCache,
    ) {
    }

    public function create(Jwt $jwt): GetAuth0Response
    {
        if ($this->cachePool->hasItem(CacheKey::get($jwt))) {
            return $this->fromCache;
        }

        return $this->fromAuth0;
    }
}
