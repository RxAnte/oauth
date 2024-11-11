<?php

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository;

readonly class TokenRepositoryConfig
{
    public function __construct(
        public int $expireInSeconds,
        public string $cacheKeyPrefix = 'session_id_user_token-',
    ) {
    }

    public function getSessionIdCacheKey(string $sessionId): string
    {
        return $this->cacheKeyPrefix . $sessionId;
    }
}
