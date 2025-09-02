<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\CustomAuthenticationHook;
use RxAnte\OAuth\CustomAuthenticationHookFactory;
use RxAnte\OAuth\CustomAuthenticationResult;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('RequireOauthTokenHeaderMiddleware', function (): void {
    uses()->group('RequireOauthTokenHeaderMiddleware');

    class RequireOauthTokenHeaderMiddlewareMockStack
    {
        public readonly ResponseFactoryInterface&MockInterface $responseFactory;
        public readonly OauthUserInfoRepositoryInterface&MockInterface $userInfoRepository;
        public readonly CustomAuthenticationHookFactory&MockInterface $authHookFactory;
        public readonly ServerRequestInterface&MockInterface $request;
        public readonly ServerRequestInterface&MockInterface $requestWithAttribute;
        public readonly RequestHandlerInterface&MockInterface $handler;

        public function __construct()
        {
            $this->responseFactory = Mockery::mock(
                ResponseFactoryInterface::class,
            );

            $this->setUpResponseFactory();

            $this->userInfoRepository = Mockery::mock(
                OauthUserInfoRepositoryInterface::class,
            );

            $this->authHookFactory = Mockery::mock(
                CustomAuthenticationHookFactory::class,
            );

            $this->request = Mockery::mock(
                ServerRequestInterface::class,
            );

            $this->requestWithAttribute = Mockery::mock(
                ServerRequestInterface::class,
            );

            $this->handler = Mockery::mock(
                RequestHandlerInterface::class,
            );
        }

        /** @phpstan-ignore-next-line */
        private array $responseWithHeaderCalls = [];

        private string $responseBody = '';

        private int $responseStatus = 200;

        private function setUpResponseFactory(): void
        {
            $body = Mockery::mock(StreamInterface::class);

            $body->allows('write')
                ->andReturnUsing(function (string $string): int {
                    $this->responseBody .= $string;

                    return strlen($string);
                });

            $body->allows('__toString')
                ->andReturnUsing(function () {
                    return $this->responseBody;
                });

            $response = Mockery::mock(ResponseInterface::class);

            $response->allows('withHeader')
                ->andReturnUsing(function (
                    string $name,
                    $value,
                ) use (
                    $response,
                ): ResponseInterface {
                    $this->responseWithHeaderCalls[] = [$name => $value];

                    return $response;
                });

            $response->allows('getHeaders')
                ->andReturnUsing(function () {
                    return $this->responseWithHeaderCalls;
                });

            $response->allows('getBody')->andReturn($body);

            $response->allows('withStatus')
                ->andReturnUsing(function (int $code) use (
                    $response,
                ): ResponseInterface {
                    $this->responseStatus = $code;

                    return $response;
                });

            $response->allows('getStatusCode')
                ->andReturnUsing(function () {
                    return $this->responseStatus;
                });

            $this->responseFactory->allows('createResponse')
                ->andReturnUsing(
                    function (
                        int $code = 200,
                        string $reasonPhrase = '',
                    ) use ($response): ResponseInterface {
                        return $response;
                    },
                );
        }

        public function setUserInfoExpectation(OauthUserInfo $userInfo): void
        {
            $this->userInfoRepository
                ->expects('getUserInfoFromRequestToken')
                ->with($this->request)
                ->andReturn($userInfo);
        }

        public function expectAuthHookFactory(
            OauthUserInfo $withUserInfo,
            ServerRequestInterface|null $customRequest = null,
            ResponseInterface|null $customResponse = null,
        ): void {
            $customAuthHook = Mockery::mock(
                CustomAuthenticationHook::class,
            );

            $customAuthHook->expects('process')
                ->andReturnUsing(function (
                    OauthUserInfo $userInfo,
                    ServerRequestInterface $request,
                    ResponseInterface $defaultAccessDeniedResponse,
                ) use (
                    $withUserInfo,
                    $customRequest,
                    $customResponse,
                ): CustomAuthenticationResult {
                    expect($userInfo)->toBe($withUserInfo);
                    expect($request)->toBe($this->request);

                    $this->expectResponseToBeDefaultAccessDenied(
                        response: $defaultAccessDeniedResponse,
                    );

                    return new CustomAuthenticationResult(
                        request: $customRequest,
                        response: $customResponse,
                    );
                });

            $this->authHookFactory->expects('create')
                ->andReturn($customAuthHook);
        }

        public function expectRequestAttributeUserInfo(
            OauthUserInfo $userInfo,
        ): void {
            $this->request->expects('withAttribute')
                ->with('oauthUserInfo', $userInfo)
                ->andReturn($this->request);
        }

        public function expectResponseToBeDefaultAccessDenied(
            ResponseInterface $response,
        ): void {
            expect($response->getStatusCode())->toBe(401);

            expect($response->getHeaders())->toBe([
                ['Content-type' => 'application/json'],
            ]);

            expect($response->getBody()->__toString())->toBe(
                '{"error":"access_denied","error_description":"A valid bearer token is required to access this resource","message":"A valid bearer token is required to access this resource"}',
            );
        }

        public function expectHandlerCall(
            ServerRequestInterface $withRequest,
            ResponseInterface $returnResponse,
        ): void {
            $this->handler->expects('handle')
                ->with($withRequest)
                ->andReturn($returnResponse);
        }
    }

    it(
        'returns 401 if userInfo is not valid',
        function (): void {
            $mockStack = new RequireOauthTokenHeaderMiddlewareMockStack();

            $mockStack->setUserInfoExpectation(new OauthUserInfo());

            $middleware = new RequireOauthTokenHeaderMiddleware(
                responseFactory: $mockStack->responseFactory,
                userInfoRepository: $mockStack->userInfoRepository,
                authHookFactory: $mockStack->authHookFactory,
            );

            $response = $middleware->process(
                request: $mockStack->request,
                handler: $mockStack->handler,
            );

            $mockStack->expectResponseToBeDefaultAccessDenied(
                response: $response,
            );
        },
    );

    it(
        'returns custom auth response',
        function (): void {
            $customResponse = Mockery::mock(ResponseInterface::class);

            $userInfo = new OauthUserInfo(true);

            $mockStack = new RequireOauthTokenHeaderMiddlewareMockStack();

            $mockStack->setUserInfoExpectation(userInfo: $userInfo);

            $mockStack->expectAuthHookFactory(
                withUserInfo: $userInfo,
                customResponse: $customResponse,
            );

            $mockStack->expectRequestAttributeUserInfo(userInfo: $userInfo);

            $middleware = new RequireOauthTokenHeaderMiddleware(
                responseFactory: $mockStack->responseFactory,
                userInfoRepository: $mockStack->userInfoRepository,
                authHookFactory: $mockStack->authHookFactory,
            );

            $response = $middleware->process(
                request: $mockStack->request,
                handler: $mockStack->handler,
            );

            expect($response)->toBe($customResponse);
        },
    );

    it(
        'returns response from handler',
        function (): void {
            $returnResponse = Mockery::mock(ResponseInterface::class);

            $customRequest = Mockery::mock(
                ServerRequestInterface::class,
            );

            $userInfo = new OauthUserInfo(true);

            $mockStack = new RequireOauthTokenHeaderMiddlewareMockStack();

            $mockStack->setUserInfoExpectation(userInfo: $userInfo);

            $mockStack->expectAuthHookFactory(
                withUserInfo: $userInfo,
                customRequest: $customRequest,
            );

            $mockStack->expectHandlerCall(
                $customRequest,
                $returnResponse,
            );

            $mockStack->expectRequestAttributeUserInfo(userInfo: $userInfo);

            $middleware = new RequireOauthTokenHeaderMiddleware(
                responseFactory: $mockStack->responseFactory,
                userInfoRepository: $mockStack->userInfoRepository,
                authHookFactory: $mockStack->authHookFactory,
            );

            $response = $middleware->process(
                request: $mockStack->request,
                handler: $mockStack->handler,
            );

            expect($response)->toBe($returnResponse);
        },
    );
});
