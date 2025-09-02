<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\AccessDeniedResponseFactory;
use RxAnte\OAuth\CustomAuthenticationHook;
use RxAnte\OAuth\CustomAuthenticationHookFactory;
use RxAnte\OAuth\CustomAuthenticationResult;
use RxAnte\OAuth\RequireOauthSessionAccessDeniedMiddleware;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('RequireOauthSessionAccessDeniedMiddleware', function (): void {
    uses()->group('RequireOauthSessionAccessDeniedMiddleware');

    readonly class RequireOauthSessionAccessDeniedMiddlewareMockStack
    {
        public ResponseInterface&MockInterface $accessDeniedResponse;
        public CustomAuthenticationHookFactory&MockInterface $authHookFactory;
        public OauthUserInfoRepositoryInterface&MockInterface $userInfoRepository;
        public AccessDeniedResponseFactory&MockInterface $accessDeniedResponseFactory;
        public ServerRequestInterface&MockInterface $request;
        public RequestHandlerInterface&MockInterface $handler;

        public function __construct()
        {
            $this->accessDeniedResponse = Mockery::mock(
                ResponseInterface::class,
            );

            $this->authHookFactory = Mockery::mock(
                CustomAuthenticationHookFactory::class,
            );

            $this->userInfoRepository = Mockery::mock(
                OauthUserInfoRepositoryInterface::class,
            );

            $this->accessDeniedResponseFactory = Mockery::mock(
                AccessDeniedResponseFactory::class,
            );

            $this->expectAccessDeniedResponseFactory();

            $this->request = Mockery::mock(
                ServerRequestInterface::class,
            );

            $this->handler = Mockery::mock(
                RequestHandlerInterface::class,
            );
        }

        private function expectAccessDeniedResponseFactory(): void
        {
            $this->accessDeniedResponseFactory->expects('create')
                ->andReturn($this->accessDeniedResponse);
        }

        public function expectUserInfoRepositoryCall(
            OauthUserInfo $userInfo,
        ): void {
            $this->userInfoRepository
                ->expects('getUserInfoFromRequestSession')
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
                    expect($defaultAccessDeniedResponse)->toBe(
                        $this->accessDeniedResponse,
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
        'returns access denied response if userinfo is invalid',
        function (): void {
            $mockStack = new RequireOauthSessionAccessDeniedMiddlewareMockStack();

            $mockStack->expectUserInfoRepositoryCall(
                new OauthUserInfo(),
            );

            $middleware = new RequireOauthSessionAccessDeniedMiddleware(
                authHookFactory: $mockStack->authHookFactory,
                userInfoRepository: $mockStack->userInfoRepository,
                accessDeniedResponseFactory: $mockStack->accessDeniedResponseFactory,
            );

            $response = $middleware->process(
                request: $mockStack->request,
                handler: $mockStack->handler,
            );

            expect($response)->toBe(
                $mockStack->accessDeniedResponse,
            );
        },
    );

    it(
        'returns custom auth response',
        function (): void {
            $customResponse = Mockery::mock(ResponseInterface::class);

            $userInfo = new OauthUserInfo(isValid: true);

            $mockStack = new RequireOauthSessionAccessDeniedMiddlewareMockStack();

            $mockStack->expectUserInfoRepositoryCall(userInfo: $userInfo);

            $mockStack->expectAuthHookFactory(
                withUserInfo: $userInfo,
                customResponse: $customResponse,
            );

            $mockStack->expectRequestAttributeUserInfo(userInfo: $userInfo);

            $middleware = new RequireOauthSessionAccessDeniedMiddleware(
                authHookFactory: $mockStack->authHookFactory,
                userInfoRepository: $mockStack->userInfoRepository,
                accessDeniedResponseFactory: $mockStack->accessDeniedResponseFactory,
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

            $userInfo = new OauthUserInfo(isValid: true);

            $mockStack = new RequireOauthSessionAccessDeniedMiddlewareMockStack();

            $mockStack->expectUserInfoRepositoryCall(userInfo: $userInfo);

            $mockStack->expectAuthHookFactory(
                withUserInfo: $userInfo,
                customRequest: $customRequest,
            );

            $mockStack->expectRequestAttributeUserInfo(userInfo: $userInfo);

            $mockStack->expectHandlerCall(
                $customRequest,
                $returnResponse,
            );

            $middleware = new RequireOauthSessionAccessDeniedMiddleware(
                authHookFactory: $mockStack->authHookFactory,
                userInfoRepository: $mockStack->userInfoRepository,
                accessDeniedResponseFactory: $mockStack->accessDeniedResponseFactory,
            );

            $response = $middleware->process(
                request: $mockStack->request,
                handler: $mockStack->handler,
            );

            expect($response)->toBe($returnResponse);
        },
    );
});
