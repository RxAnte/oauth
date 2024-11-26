<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use RxAnte\OAuth\Handlers\Auth0\Auth0Config;
use RxAnte\OAuth\UserInfo\Jwt;

readonly class FetchUserInfoFactory
{
    public function __construct(
        private Auth0Config $config,
        private FetchUserInfoNoOp $noOp,
        private FetchUserInfoForM2M $forM2M,
        private FetchUserInfoFromAuth0 $fromAuth0,
    ) {
    }

    public function create(Jwt $jwt): FetchUserInfo
    {
        if ($jwt->rawToken === '') {
            return $this->noOp;
        }

        if ($this->config->m2mSubjectIsAuthorized($jwt->sub)) {
            return $this->forM2M;
        }

        return $this->fromAuth0;
    }
}
