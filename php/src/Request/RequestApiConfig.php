<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Request;

use function filter_var;
use function rtrim;

use const FILTER_VALIDATE_URL;

readonly class RequestApiConfig
{
    public string $requestBaseUrl;

    public function __construct(
        string $requestBaseUrl = '',
    ) {
        $this->requestBaseUrl = rtrim($requestBaseUrl, '/');
    }

    public function createUrl(
        string $uri,
        QueryParams $queryParams,
    ): string {
        $isFullUrl = filter_var($uri, FILTER_VALIDATE_URL) !== false;

        $uri .= $queryParams->asQueryString();

        if ($isFullUrl) {
            return $uri;
        }

        return $this->requestBaseUrl . $uri;
    }
}
