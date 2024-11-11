<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use RxAnte\OAuth\UserInfo\Jwt;

readonly class CacheResponseWithSystemCache implements CacheResponse
{
    public function __construct(
        private ClockInterface $clock,
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    public function cache(Jwt $jwt, Auth0Response $response): void
    {
        $dateTime = $this->clock->now()->setTimestamp(
            $jwt->exp,
        );

        $this->cachePool->save(
            $this->cachePool->getItem(CacheKey::get($jwt))
                ->set($response)
                // We only want to cache the response for as long as it's valid
                ->expiresAt($dateTime),
        );
    }
}
