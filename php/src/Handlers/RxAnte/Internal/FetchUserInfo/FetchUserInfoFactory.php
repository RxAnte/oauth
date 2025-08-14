<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\Handlers\RxAnte\Internal\EmptyJwt;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;

use function is_string;

readonly class FetchUserInfoFactory
{
    public function __construct(
        private RxAnteConfig $config,
        private FetchUserInfoNoOp $noOp,
        private FetchUserInfoForM2M $forM2M,
        private FetchUserInfoFromRxAnteAuth $fromRxAnteAuth,
    ) {
    }

    public function create(JwtToken $jwt): FetchUserInfo
    {
        if ($jwt instanceof EmptyJwt) {
            return $this->noOp;
        }

        $sub = $jwt->claims()->get('sub', '');
        $sub = is_string($sub) ? $sub : '';

        if ($this->config->m2mSubjectIsAuthorized($sub)) {
            return $this->forM2M;
        }

        return $this->fromRxAnteAuth;
    }
}
