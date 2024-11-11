<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0;

use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo\FetchUserInfoFactory;
use RxAnte\OAuth\Handlers\Auth0\Internal\JwtFactory;
use RxAnte\OAuth\Handlers\Common\ExtractToken;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

readonly class Auth0UserInfoRepository implements OauthUserInfoRepositoryInterface
{
    public function __construct(
        private JwtFactory $jwtFactory,
        private ExtractToken $extractToken,
        private FetchUserInfoFactory $fetchUserInfoFactory,
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
        // TODO: Implement getUserInfoFromRequestSession() method.
    }
}
