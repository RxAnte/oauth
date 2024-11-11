<?php

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository;

use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use RuntimeException;

readonly class SetAccessTokenFromSessionId
{
    public function __construct(
        private ClockInterface $clock,
        private TokenRepositoryConfig $config,
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    public function set(
        string $sessionId,
        AccessTokenInterface $accessToken,
    ): void {
        if ($sessionId === '') {
            throw new RuntimeException(
                '$sessionId must not be empty',
            );
        }

        $now = $this->clock->now();

        $nowTimeStamp = $now->getTimestamp();

        $expiresTimeStamp = $nowTimeStamp + $this->config->expireInSeconds;

        $expires = $now->setTimestamp($expiresTimeStamp);

        $cacheItem = $this->cachePool->getItem(
            $this->config->getSessionIdCacheKey($sessionId),
        )
            ->set($accessToken)
            ->expiresAt($expires);

        $status = $this->cachePool->save($cacheItem);

        if ($status) {
            return;
        }

        throw new RuntimeException(
            'Unable to store access token',
        );
    }
}
