<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

readonly class CustomAuthenticationHookNoOp implements CustomAuthenticationHook
{
    public function process(
        OauthUserInfo $userInfo,
        ServerRequestInterface $request,
        ResponseInterface $defaultAccessDeniedResponse,
    ): CustomAuthenticationResult {
        return new CustomAuthenticationResult();
    }
}
