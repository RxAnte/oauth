<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\CustomAuthenticationHook;
use RxAnte\OAuth\CustomAuthenticationHookFactory;
use RxAnte\OAuth\CustomAuthenticationResult;
use RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware;
use RxAnte\OAuth\SendToLoginResponseFactory;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('RequireOauthSessionLoginRedirectMiddleware', function (): void {
    uses()->group('RequireOauthSessionLoginRedirectMiddleware');

    class RequireOauthSessionLoginRedirectMiddlewareMockStack
    {
        public readonly CustomAuthenticationHookFactory&MockInterface $authHookFactory;
        public readonly OauthUserInfoRepositoryInterface&MockInterface $userInfoRepository;
        public readonly SendToLoginResponseFactory&MockInterface $sendToLoginResponseFactory;
        public readonly ServerRequestInterface&MockInterface $request;
        public readonly RequestHandlerInterface&MockInterface $handler;

        public function __construct()
        {
            $this->authHookFactory = Mockery::mock(
                CustomAuthenticationHookFactory::class,
            );

            $this->userInfoRepository = Mockery::mock(
                OauthUserInfoRepositoryInterface::class,
            );

            $this->sendToLoginResponseFactory = Mockery::mock(
                SendToLoginResponseFactory::class,
            );

            $this->request = Mockery::mock(
                ServerRequestInterface::class,
            );

            $this->handler = Mockery::mock(
                RequestHandlerInterface::class,
            );
        }

        public function expectUserInfoRepositoryCall(
            OauthUserInfo $userInfo,
        ): void {
            $this->userInfoRepository
                ->expects('getUserInfoFromRequestSession')
                ->with($this->request)
                ->andReturn($userInfo);
        }

        public function expectSendToLogin(ResponseInterface $response): void
        {
            $this->sendToLoginResponseFactory->expects('create')
                ->with($this->request)
                ->andReturn($response);
        }

        public function expectAuthHookFactory(
            OauthUserInfo $withUserInfo,
            ResponseInterface $withDefaultAccessDeniedResponse,
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
                    $withDefaultAccessDeniedResponse,
                ): CustomAuthenticationResult {
                    expect($userInfo)->toBe($withUserInfo);
                    expect($request)->toBe($this->request);
                    expect($defaultAccessDeniedResponse)->toBe(
                        $withDefaultAccessDeniedResponse,
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
        'returns send to login response if userinfo is invalid',
        function (): void {
            $sendToLoginResponse = Mockery::mock(
                ResponseInterface::class,
            );

            $mockStack = new RequireOauthSessionLoginRedirectMiddlewareMockStack();

            $mockStack->expectUserInfoRepositoryCall(
                new OauthUserInfo(),
            );

            $mockStack->expectSendToLogin($sendToLoginResponse);

            $middleware = new RequireOauthSessionLoginRedirectMiddleware(
                authHookFactory: $mockStack->authHookFactory,
                userInfoRepository: $mockStack->userInfoRepository,
                sendToLoginResponseFactory: $mockStack->sendToLoginResponseFactory,
            );

            $response = $middleware->process(
                request: $mockStack->request,
                handler: $mockStack->handler,
            );

            expect($response)->toBe($sendToLoginResponse);
        },
    );

    it(
        'returns custom auth response',
        function (): void {
            $sendToLoginResponse = Mockery::mock(
                ResponseInterface::class,
            );

            $customResponse = Mockery::mock(ResponseInterface::class);

            $userInfo = new OauthUserInfo(isValid: true);

            $mockStack = new RequireOauthSessionLoginRedirectMiddlewareMockStack();

            $mockStack->expectUserInfoRepositoryCall(userInfo: $userInfo);

            $mockStack->expectSendToLogin($sendToLoginResponse);

            $mockStack->expectAuthHookFactory(
                withUserInfo: $userInfo,
                withDefaultAccessDeniedResponse: $sendToLoginResponse,
                customResponse: $customResponse,
            );

            $mockStack->expectRequestAttributeUserInfo(userInfo: $userInfo);

            $middleware = new RequireOauthSessionLoginRedirectMiddleware(
                authHookFactory: $mockStack->authHookFactory,
                userInfoRepository: $mockStack->userInfoRepository,
                sendToLoginResponseFactory: $mockStack->sendToLoginResponseFactory,
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
            $sendToLoginResponse = Mockery::mock(
                ResponseInterface::class,
            );

            $returnResponse = Mockery::mock(ResponseInterface::class);

            $customRequest = Mockery::mock(
                ServerRequestInterface::class,
            );

            $userInfo = new OauthUserInfo(isValid: true);

            $mockStack = new RequireOauthSessionLoginRedirectMiddlewareMockStack();

            $mockStack->expectUserInfoRepositoryCall(userInfo: $userInfo);

            $mockStack->expectSendToLogin($sendToLoginResponse);

            $mockStack->expectAuthHookFactory(
                withUserInfo: $userInfo,
                withDefaultAccessDeniedResponse: $sendToLoginResponse,
                customRequest: $customRequest,
            );

            $mockStack->expectRequestAttributeUserInfo(userInfo: $userInfo);

            $mockStack->expectHandlerCall(
                $customRequest,
                $returnResponse,
            );

            $middleware = new RequireOauthSessionLoginRedirectMiddleware(
                authHookFactory: $mockStack->authHookFactory,
                userInfoRepository: $mockStack->userInfoRepository,
                sendToLoginResponseFactory: $mockStack->sendToLoginResponseFactory,
            );

            $response = $middleware->process(
                request: $mockStack->request,
                handler: $mockStack->handler,
            );

            expect($response)->toBe($returnResponse);
        },
    );
});
