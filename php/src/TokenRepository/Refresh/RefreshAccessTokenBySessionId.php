<?php

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository\Refresh;

use RxAnte\OAuth\Handlers\Auth0\TokenRefresh\GetRefreshedAccessTokenFromAuth0;
use RxAnte\OAuth\TokenRepository\GetAccessTokenBySessionId;
use RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock;
use RxAnte\OAuth\TokenRepository\SetAccessTokenFromSessionId;

readonly class RefreshAccessTokenBySessionId
{
    public function __construct(
        private RefreshLock $refreshLock,
        private GetAccessTokenBySessionId $getToken,
        private SetAccessTokenFromSessionId $setToken,
        private GetRefreshedAccessTokenFromAuth0 $getRefreshedAccessToken,
    ) {
    }

    public function refresh(string $sessionId): void
    {
        $token = $this->getToken->get($sessionId);

        // To ensure only one request is refreshing the token we await a lock
        $this->refreshLock->acquire($token->getToken());

        /**
         * Now we check the token in the store again to make sure the token
         * wasn't already refreshed by another request
         */
        $tokenCheck = $this->getToken->get($sessionId);

        // It looks like the token was already refreshed while we awaited a lock
        if ($tokenCheck->getToken() !== $token->getToken()) {
            $this->refreshLock->release($token->getToken());

            return;
        }

        $newToken = $this->getRefreshedAccessToken->get($token);

        // If there is no token, the refresh was unsuccessful, and so we won't save
        if ($newToken === null) {
            $this->refreshLock->release($token->getToken());

            return;
        }

        // WE HAVE A NEW TOKEN! YAY! Now set it to the token store
        $this->setToken->set($sessionId, $newToken);

        $this->refreshLock->release($token->getToken());
    }
}
