<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;

readonly class FetchUserInfoFactory
{
    public function __construct(
        private FetchUserInfoNoOp $noOp,
        private FetchUserInfoFromAuth0 $fromAuth0,
    ) {
    }

    public function create(Jwt $jwt): FetchUserInfo
    {
        if ($jwt->rawToken === '') {
            return $this->noOp;
        }

        return $this->fromAuth0;
    }
}
