<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0;

use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo\FetchUserInfoFactory;
use RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo\GetUserinfoFromSessionId;
use RxAnte\OAuth\Handlers\Auth0\Internal\JwtFactory;
use RxAnte\OAuth\Handlers\Common\ExtractToken;
use RxAnte\OAuth\TokenRepository\Refresh\RefreshAccessTokenBySessionId;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

use function assert;
use function is_string;

readonly class Auth0UserInfoRepository implements OauthUserInfoRepositoryInterface
{
    public function __construct(
        private JwtFactory $jwtFactory,
        private ExtractToken $extractToken,
        private FetchUserInfoFactory $fetchUserInfoFactory,
        private RefreshAccessTokenBySessionId $refreshAccessToken,
        private GetUserinfoFromSessionId $getUserinfoFromSessionId,
        private OauthSessionTokenCookieHandler $sessionTokenCookieHandler,
    ) {
    }

    public function getUserInfoFromRequestToken(
        ServerRequestInterface $request,
    ): OauthUserInfo {
        $jwt = $this->jwtFactory->fromBearerToken(
            $this->extractToken->fromAuthHeader($request),
        );

        return $this->fetchUserInfoFactory->create($jwt)->fetch($jwt);
    }

    public function getUserInfoFromRequestSession(
        ServerRequestInterface $request,
    ): OauthUserInfo {
        $cookie = $this->sessionTokenCookieHandler->getCookieFromRequest(
            $request,
        );

        $sessionId = $cookie->getValue();

        $userInfo = $this->getUserinfoFromSessionId->get($sessionId);

        if ($userInfo->isValid) {
            return $userInfo;
        }

        // We already know it has to be a string at this point
        assert(is_string($sessionId));

        // Try refreshing the token
        $this->refreshAccessToken->refresh($sessionId);

        return $this->getUserinfoFromSessionId->get($sessionId);
    }
}
