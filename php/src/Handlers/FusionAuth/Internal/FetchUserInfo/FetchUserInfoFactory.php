<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use RxAnte\OAuth\Handlers\FusionAuth\FusionAuthConfig;
use RxAnte\OAuth\UserInfo\Jwt;

readonly class FetchUserInfoFactory
{
    public function __construct(
        private FetchUserInfoNoOp $noOp,
        private FusionAuthConfig $config,
        private FetchUserInfoForM2M $forM2M,
        private FetchUserInfoFromFusionAuth $fromFusionAuth,
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

        return $this->fromFusionAuth;
    }
}
