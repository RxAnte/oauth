<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse;

use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponseWrapper;

readonly class CacheResponseFactory
{
    public function __construct(
        private CacheResponseNoOp $noOp,
        private CacheResponseWithSystemCache $withSystemCache,
    ) {
    }

    public function create(
        RxAnteResponseWrapper $responseWrapper,
    ): CacheResponse {
        if ($responseWrapper->response->isNotValid()) {
            return $this->noOp;
        }

        if ($responseWrapper->isFromCache) {
            return $this->noOp;
        }

        return $this->withSystemCache;
    }
}
