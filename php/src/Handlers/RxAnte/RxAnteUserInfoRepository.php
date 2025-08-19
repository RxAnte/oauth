<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use Psr\Container\ContainerInterface;
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

// phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed

readonly class RxAnteUserInfoRepository implements OauthUserInfoRepositoryInterface
{
    public function __construct(
        private JwtFactory $jwtFactory,
        private ContainerInterface $container,
        private FetchUserInfoFactory $fetchUserInfoFactory,
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

        /**
         * We're getting this on-demand so that in contexts where we don't
         * need it, it doesn't need to be provided
         */
        $getUserInfoFromSessionId = $this->container->get(
            GetUserInfoFromSessionId::class,
        );

        $userInfo = $getUserInfoFromSessionId->get($sessionId);

        if ($userInfo->isValid) {
            return $userInfo;
        }

        // Try refreshing the token

        /**
         * We're getting this on-demand so that in contexts where we don't
         * need it, it doesn't need to be provided
         */
        $refresh = $this->container->get(RefreshAccessTokenBySessionId::class);

        $refresh->refresh($sessionId);

        return $getUserInfoFromSessionId->get($sessionId);
    }
}
