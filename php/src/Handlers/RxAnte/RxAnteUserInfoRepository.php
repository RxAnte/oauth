<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetUserInfoFromSessionId;
use RxAnte\OAuth\TokenRepository\Refresh\RefreshAccessTokenBySessionId;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

use function var_dump;

readonly class RxAnteUserInfoRepository implements OauthUserInfoRepositoryInterface
{
    public function __construct(
        private RefreshAccessTokenBySessionId $refreshAccessToken,
        private GetUserInfoFromSessionId $getUserInfoFromSessionId,
        private OauthSessionTokenCookieHandler $sessionTokenCookieHandler,
    ) {
    }

    public function getUserInfoFromRequestToken(
        ServerRequestInterface $request,
    ): OauthUserInfo {
        // TODO: Implement getUserInfoFromRequestToken() method.
        var_dump('TODO: Implement getUserInfoFromRequestToken() method.');
        die;
    }

    public function getUserInfoFromRequestSession(
        ServerRequestInterface $request,
    ): OauthUserInfo {
        $cookie = $this->sessionTokenCookieHandler->getCookieFromRequest(
            $request,
        );

        $sessionId = $cookie->getValue() ?? '';

        $userInfo = $this->getUserInfoFromSessionId->get($sessionId);

        if ($userInfo->isValid) {
            return $userInfo;
        }

        // Try refreshing the token
        $this->refreshAccessToken->refresh($sessionId);

        return $this->getUserInfoFromSessionId->get($sessionId);
    }
}
