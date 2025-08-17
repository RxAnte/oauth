<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class CacheResponseFactory
{
    public function __construct(
        private CacheResponseNoOp $noOp,
        private CacheResponseWithSystemCache $withSystemCache,
    ) {
    }

    public function create(Auth0Response $response): CacheResponse
    {
        if ($response->isNotValid()) {
            return $this->noOp;
        }

        return $this->withSystemCache;
    }
}
