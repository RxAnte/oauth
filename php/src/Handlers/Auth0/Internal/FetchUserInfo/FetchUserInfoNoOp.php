<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

readonly class FetchUserInfoNoOp implements FetchUserInfo
{
    public function fetch(Jwt $jwt): OauthUserInfo
    {
        return new OauthUserInfo();
    }
}
