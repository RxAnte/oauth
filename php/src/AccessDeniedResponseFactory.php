<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

readonly class AccessDeniedResponseFactory
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private CustomResponseFactory|null $customResponseFactory,
    ) {
    }

    public function create(string $thing): ResponseInterface
    {
        return match ($this->customResponseFactory) {
            null => $this->createResponse(),
            default => $this->customResponseFactory->create(),
        };
    }

    private function createResponse(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(403);

        $response->getBody()->write('Access Denied');

        return $response;
    }
}
