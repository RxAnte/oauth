<?php

declare(strict_types=1);

namespace RxAnte\OAuth\UserInfo;

use Psr\Http\Message\ServerRequestInterface;

// phpcs:disable SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming.SuperfluousSuffix

interface OauthUserInfoRepositoryInterface
{
    public function getUserInfoFromRequestToken(
        ServerRequestInterface $request,
    ): OauthUserInfo;

    public function getUserInfoFromRequestSession(
        ServerRequestInterface $request,
    ): OauthUserInfo;
}
