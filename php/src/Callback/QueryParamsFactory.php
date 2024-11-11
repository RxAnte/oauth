<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Callback;

use Psr\Http\Message\ServerRequestInterface;

readonly class QueryParamsFactory
{
    public function createFromRequest(
        ServerRequestInterface $request,
    ): QueryParams {
        $queryParams = $request->getQueryParams();

        return new QueryParams(
            code: $queryParams['code'] ?? '',
            state: $queryParams['state'] ?? '',
        );
    }
}
