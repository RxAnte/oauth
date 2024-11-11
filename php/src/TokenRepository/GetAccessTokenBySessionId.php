<?php

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository;

use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Cache\CacheItemPoolInterface;

use function assert;

readonly class GetAccessTokenBySessionId
{
    public function __construct(
        private TokenRepositoryConfig $config,
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    public function get(string $sessionId): AccessTokenInterface
    {
        $cacheItem = $this->cachePool->getItem(
            $this->config->getSessionIdCacheKey($sessionId),
        );

        if (! $cacheItem->isHit()) {
            return new EmptyAccessToken();
        }

        $token = $cacheItem->get();

        assert($token instanceof AccessTokenInterface);

        return $token;
    }
}
