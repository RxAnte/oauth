<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use RxAnte\OAuth\Handlers\Auth0\Internal\JwtFactory;
use RxAnte\OAuth\TokenRepository\EmptyAccessToken;
use RxAnte\OAuth\TokenRepository\TokenRepository;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class GetUserinfoFromSessionId
{
    public function __construct(
        private JwtFactory $jwtFactory,
        private TokenRepository $tokenRepository,
        private FetchUserInfoFactory $fetchUserInfoFactory,
    ) {
    }

    public function get(string|null $sessionId): OauthUserInfo
    {
        if ($sessionId === null) {
            return new OauthUserInfo();
        }

        $token = $this->tokenRepository->getTokenBySessionId(
            $sessionId,
        );

        if ($token instanceof EmptyAccessToken) {
            return new OauthUserInfo();
        }

        $jwt = $this->jwtFactory->fromBearerToken(
            'Bearer ' . $token->getToken(),
        );

        return $this->fetchUserInfoFactory->create($jwt)->fetch($jwt);
    }
}
