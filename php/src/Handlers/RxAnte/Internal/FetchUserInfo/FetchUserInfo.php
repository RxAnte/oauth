<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

interface FetchUserInfo
{
    public function fetch(JwtToken $jwt): OauthUserInfo;
}
