<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;
use RxAnte\OAuth\RequireM2MOauthTokenHeaderMiddleware;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

describe('RequireM2MOauthTokenHeaderMiddleware', function (): void {
    uses()->group('RequireM2MOauthTokenHeaderMiddleware');

    it(
        'runs handle and returns 403',
        function (): void {
            $requestResponseFactory = new Psr17Factory();

            $requireOauthToken = Mockery::mock(
                RequireOauthTokenHeaderMiddleware::class,
            );

            $sut = new RequireM2MOauthTokenHeaderMiddleware(
                config: new RxAnteConfig(
                    wellKnownUrl: 'foo',
                ),
                responseFactory: $requestResponseFactory,
                requireOauthToken: $requireOauthToken,
            );

            $request = $requestResponseFactory->createServerRequest(
                'GET',
                '/foo/bar',
            )->withAttribute(
                'oauthUserInfo',
                new OauthUserInfo(
                    true,
                    'foo',
                ),
            );

            $handler = Mockery::mock(RequestHandlerInterface::class);

            $response = $sut->process(
                request: $request,
                handler: $handler,
            );

            $requireOauthToken->shouldNotHaveBeenCalled();

            $handler->shouldNotHaveBeenCalled();

            expect($response->getBody()->__toString())->toBe(
                '{"error":"access_denied","error_description":"A valid M2M bearer token is required to access this resource","message":"A valid M2M bearer token is required to access this resource"}',
            );

            expect($response->getStatusCode())->toBe(403);

            expect($response->getHeaders())->toBe([
                'Content-type' => ['application/json'],
            ]);
        },
    );

    it(
        'runs handle and returns handler method',
        function (): void {
            $requestResponseFactory = new Psr17Factory();

            $requireOauthToken = Mockery::mock(
                RequireOauthTokenHeaderMiddleware::class,
            );

            $sut = new RequireM2MOauthTokenHeaderMiddleware(
                config: new RxAnteConfig(
                    wellKnownUrl: 'foo',
                    m2mAuthorizedSubjects: ['foo-bar'],
                ),
                responseFactory: $requestResponseFactory,
                requireOauthToken: $requireOauthToken,
            );

            $request = $requestResponseFactory->createServerRequest(
                'GET',
                '/foo/bar',
            )->withAttribute(
                'oauthUserInfo',
                new OauthUserInfo(
                    true,
                    'foo-bar',
                ),
            );

            $handlerResponse = Mockery::mock(ResponseInterface::class);

            $handler = Mockery::mock(RequestHandlerInterface::class);

            $handler->expects('handle')
                ->with($request)
                ->andReturn($handlerResponse);

            $response = $sut->process(
                request: $request,
                handler: $handler,
            );

            $requireOauthToken->shouldNotHaveBeenCalled();

            expect($response)->toBe($handlerResponse);
        },
    );

    it(
        'calls process on require oauth token header middleware',
        function (): void {
            $middlewareResponse = Mockery::mock(
                ResponseInterface::class,
            );

            $requestResponseFactory = new Psr17Factory();

            $request = $requestResponseFactory->createServerRequest(
                'GET',
                '/foo/bar',
            );

            $requireOauthToken = Mockery::mock(
                RequireOauthTokenHeaderMiddleware::class,
            );

            $sut = new RequireM2MOauthTokenHeaderMiddleware(
                config: new RxAnteConfig(
                    wellKnownUrl: 'foo',
                ),
                responseFactory: $requestResponseFactory,
                requireOauthToken: $requireOauthToken,
            );

            $requireOauthToken->expects('process')
                ->with($request, $sut)
                ->andReturn($middlewareResponse);

            $handler = Mockery::mock(RequestHandlerInterface::class);

            $response = $sut->process(
                request: $request,
                handler: $handler,
            );

            $handler->shouldNotHaveBeenCalled();

            expect($response)->toBe($middlewareResponse);
        },
    );
});
