<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Request;

readonly class RequestProperties
{
    public function __construct(
        public string $uri = '',
        public RequestMethod $method = RequestMethod::GET,
        public QueryParams $queryParams = new QueryParams(),
        public Payload $payload = new Payload(),
    ) {
    }
}
