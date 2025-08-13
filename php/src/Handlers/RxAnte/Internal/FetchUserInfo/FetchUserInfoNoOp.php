<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

readonly class FetchUserInfoNoOp implements FetchUserInfo
{
    public function fetch(JwtToken $jwt): OauthUserInfo
    {
        return new OauthUserInfo();
    }
}
