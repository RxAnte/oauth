<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse;

use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\JwtFactory;
use RxAnte\OAuth\TokenRepository\EmptyAccessToken;
use RxAnte\OAuth\TokenRepository\TokenRepository;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

readonly class GetUserInfoFromSessionId
{
    public function __construct(
        private JwtFactory $jwtFactory,
        private TokenRepository $tokenRepository,
        private FetchUserInfoFactory $fetchUserInfoFactory,
    ) {
    }

    public function get(string|null $sessionId): OauthUserInfo
    {
        if ($sessionId === null || $sessionId === '') {
            return new OauthUserInfo();
        }

        $token = $this->tokenRepository->getTokenBySessionId(
            $sessionId,
        );

        if ($token instanceof EmptyAccessToken) {
            return new OauthUserInfo();
        }

        $jwt = $this->jwtFactory->createFromToken($token->getToken());

        return $this->fetchUserInfoFactory->create($jwt)->fetch($jwt);
    }
}
