<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RxAnte\OAuth\AccessDeniedResponseFactory;
use RxAnte\OAuth\CustomResponseFactory;

describe('AccessDeniedResponseFactory', function (): void {
    uses()->group('AccessDeniedResponseFactory');

    it(
        'creates a 403 response with "Access Denied" when no custom factory is provided',
        function (): void {
            $response = Mockery::mock(ResponseInterface::class);

            $body = Mockery::mock(StreamInterface::class);

            $response->shouldReceive('getBody')->andReturn(
                $body,
            );

            $body->shouldReceive('write')
                ->with('Access Denied')
                ->once();

            $responseFactory = Mockery::mock(
                ResponseFactoryInterface::class,
            );

            $responseFactory->shouldReceive('createResponse')
                ->with(403)
                ->andReturn($response);

            $factory = new AccessDeniedResponseFactory(
                $responseFactory,
                null,
            );

            $result = $factory->create();

            expect($result)->toBe($response);
        },
    );

    it(
        'delegates to CustomResponseFactory when provided',
        function (): void {
            $customResponse = Mockery::mock(ResponseInterface::class);

            $customFactory = Mockery::mock(
                CustomResponseFactory::class,
            );

            $customFactory->shouldReceive('create')
                ->andReturn($customResponse);

            $responseFactory = Mockery::mock(
                ResponseFactoryInterface::class,
            );

            $factory = new AccessDeniedResponseFactory(
                $responseFactory,
                $customFactory,
            );

            $result = $factory->create();

            expect($result)->toBe($customResponse);
        },
    );
});
