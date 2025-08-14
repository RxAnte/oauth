<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetUserInfoFromSessionId;
use RxAnte\OAuth\Handlers\RxAnte\Internal\JwtFactory;
use RxAnte\OAuth\TokenRepository\Refresh\RefreshAccessTokenBySessionId;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

use function preg_replace;
use function trim;

readonly class RxAnteUserInfoRepository implements OauthUserInfoRepositoryInterface
{
    public function __construct(
        private JwtFactory $jwtFactory,
        private FetchUserInfoFactory $fetchUserInfoFactory,
        private RefreshAccessTokenBySessionId $refreshAccessToken,
        private GetUserInfoFromSessionId $getUserInfoFromSessionId,
        private OauthSessionTokenCookieHandler $sessionTokenCookieHandler,
    ) {
    }

    public function getUserInfoFromRequestToken(
        ServerRequestInterface $request,
    ): OauthUserInfo {
        if (! $request->hasHeader('authorization')) {
            return new OauthUserInfo();
        }

        $header = $request->getHeader('authorization');

        $jwtString = trim((string) preg_replace(
            '/^\s*Bearer\s/',
            '',
            $header[0],
        ));

        $jwt = $this->jwtFactory->createFromToken($jwtString);

        return $this->fetchUserInfoFactory->create($jwt)->fetch($jwt);
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
