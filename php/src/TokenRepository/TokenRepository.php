<?php

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository;

use League\OAuth2\Client\Token\AccessTokenInterface;
use Ramsey\Uuid\UuidFactoryInterface;
use RxAnte\OAuth\TokenRepository\Refresh\RefreshAccessTokenBySessionId;

readonly class TokenRepository
{
    public function __construct(
        private UuidFactoryInterface $uuidFactory,
        private GetAccessTokenBySessionId $getToken,
        private SetAccessTokenFromSessionId $setToken,
        private RefreshAccessTokenBySessionId $refreshAccessTokenBySessionId,
    ) {
    }

    /** @return string new session ID */
    public function createSessionIdWithAccessToken(
        AccessTokenInterface $accessToken,
    ): string {
        $sessionId = $this->uuidFactory->uuid4()->toString();

        $this->setAccessTokenFromSessionId(
            $sessionId,
            $accessToken,
        );

        return $sessionId;
    }

    public function setAccessTokenFromSessionId(
        string $sessionId,
        AccessTokenInterface $accessToken,
    ): void {
        $this->setToken->set($sessionId, $accessToken);
    }

    public function getTokenBySessionId(string $sessionId): AccessTokenInterface
    {
        return $this->getToken->get($sessionId);
    }

    public function refreshAccessTokenBySessionId(string $sessionId): void
    {
        $this->refreshAccessTokenBySessionId->refresh($sessionId);
    }
}
