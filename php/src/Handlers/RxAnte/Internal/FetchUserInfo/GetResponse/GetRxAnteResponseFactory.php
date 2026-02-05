<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheKey;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\UserInfoFetchLock;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\UserInfoFetchNoOp;

readonly class GetRxAnteResponseFactory
{
    private UserInfoFetchLock $fetchLock;

    public function __construct(
        ContainerInterface $di,
        private CacheItemPoolInterface $cachePool,
        private GetRxAnteResponseFromRxAnte $fromRxAnte,
        private GetRxAnteResponseFromSystemCache $fromCache,
    ) {
        if ($di->has(UserInfoFetchLock::class)) {
            $this->fetchLock = $di->get(UserInfoFetchLock::class);

            return;
        }

        $this->fetchLock = $di->get(UserInfoFetchNoOp::class);
    }

    public function create(JwtToken $jwt): GetRxAnteResponse
    {
        $cacheKey = CacheKey::get($jwt);

        if ($this->cachePool->hasItem($cacheKey)) {
            return $this->fromCache;
        }

        /**
         * We want to fetch userinfo only once for this cache key to prevent
         * potential throttling.
         */
        $this->fetchLock->acquire($cacheKey);

        /**
         * Do the check again in case some other process cached it while we
         * were waiting for lock
         *
         * @phpstan-ignore-next-line
         */
        if ($this->cachePool->hasItem($cacheKey)) {
            $this->fetchLock->release($cacheKey);

            return $this->fromCache;
        }

        return $this->fromRxAnte;
    }
}
