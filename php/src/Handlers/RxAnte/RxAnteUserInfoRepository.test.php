<?php

declare(strict_types=1);

use Dflydev\FigCookies\Cookie;
use GuzzleHttp\Psr7\ServerRequest;
use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Psr\Container\ContainerInterface;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfo;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetUserInfoFromSessionId;
use RxAnte\OAuth\Handlers\RxAnte\Internal\JwtFactory;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteUserInfoRepository;
use RxAnte\OAuth\TokenRepository\Refresh\RefreshAccessTokenBySessionId;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

describe(
    'RxAnteUserInfoRepository::getUserInfoFromRequestToken()',
    function (): void {
        uses()->group('RxAnteUserInfoRepository');

        it(
            'returns empty OauthUserInfo when no authorization header',
            function (): void {
                $jwtFactory = Mockery::mock(JwtFactory::class);

                $serverRequest = new ServerRequest(
                    method: 'GET',
                    uri: '/mock-uri',
                );

                $sut = new RxAnteUserInfoRepository(
                    jwtFactory: $jwtFactory,
                    container: Mockery::mock(ContainerInterface::class),
                    fetchUserInfoFactory: Mockery::mock(
                        FetchUserInfoFactory::class,
                    ),
                    sessionTokenCookieHandler: Mockery::mock(
                        OauthSessionTokenCookieHandler::class,
                    ),
                );

                $result = $sut->getUserInfoFromRequestToken(
                    $serverRequest,
                );

                expect($result->isValid)->toBeFalse();
            },
        );

        it(
            'returns empty OauthUserInfo from fetchUserInfoFactory',
            function (): void {
                $userInfo = new OauthUserInfo();

                $jwt = Mockery::mock(JwtToken::class);

                $jwtFactory = Mockery::mock(JwtFactory::class);

                $jwtFactory->expects('createFromToken')
                    ->with('mock-token')
                    ->andReturn($jwt);

                $fetchUserInfo = Mockery::mock(FetchUserInfo::class);

                $fetchUserInfo->expects('fetch')
                    ->with($jwt)
                    ->andReturn($userInfo);

                $fetchUserInfoFactory = Mockery::mock(
                    FetchUserInfoFactory::class,
                );

                $fetchUserInfoFactory->expects('create')
                    ->with($jwt)
                    ->andReturn($fetchUserInfo);

                $serverRequest = new ServerRequest(
                    method: 'GET',
                    uri: '/mock-uri',
                    headers: ['authorization' => 'Bearer mock-token'],
                );

                $sut = new RxAnteUserInfoRepository(
                    jwtFactory: $jwtFactory,
                    container: Mockery::mock(ContainerInterface::class),
                    fetchUserInfoFactory: $fetchUserInfoFactory,
                    sessionTokenCookieHandler: Mockery::mock(
                        OauthSessionTokenCookieHandler::class,
                    ),
                );

                $result = $sut->getUserInfoFromRequestToken(
                    $serverRequest,
                );

                expect($result)->toBe($userInfo);
            },
        );
    },
);

describe(
    'RxAnteUserInfoRepository::getUserInfoFromRequestSession()',
    function (): void {
        uses()->group('RxAnteUserInfoRepository');

        it(
            'returns userinfo when valid',
            function (): void {
                $userInfo = new OauthUserInfo(isValid: true);

                $serverRequest = new ServerRequest(
                    method: 'GET',
                    uri: '/mock-uri',
                );

                $cookie = new Cookie('mock-cookie');

                $sessionTokenCookieHandler = Mockery::mock(
                    OauthSessionTokenCookieHandler::class,
                );
                $sessionTokenCookieHandler
                    ->expects('getCookieFromRequest')
                    ->with($serverRequest)
                    ->andReturn($cookie);

                $getUserInfoFromSessionId = Mockery::mock(
                    GetUserInfoFromSessionId::class,
                );
                $getUserInfoFromSessionId->expects('get')
                    ->with('')
                    ->andReturn($userInfo);

                $container = Mockery::mock(ContainerInterface::class);
                $container->expects('get')
                    ->with(GetUserInfoFromSessionId::class)
                    ->andReturn($getUserInfoFromSessionId);

                $sut = new RxAnteUserInfoRepository(
                    jwtFactory: Mockery::mock(JwtFactory::class),
                    container: $container,
                    fetchUserInfoFactory: Mockery::mock(
                        FetchUserInfoFactory::class,
                    ),
                    sessionTokenCookieHandler: $sessionTokenCookieHandler,
                );

                $result = $sut->getUserInfoFromRequestSession(
                    request: $serverRequest,
                );

                expect($result)->toBe($userInfo);
            },
        );

        it(
            'attempts to refresh the token when invalid',
            function (): void {
                $userInfo2 = new OauthUserInfo(isValid: true);

                $serverRequest = new ServerRequest(
                    method: 'GET',
                    uri: '/mock-uri',
                );

                $cookie = new Cookie('mock-cookie', 'mock-id');

                $sessionTokenCookieHandler = Mockery::mock(
                    OauthSessionTokenCookieHandler::class,
                );
                $sessionTokenCookieHandler
                    ->expects('getCookieFromRequest')
                    ->with($serverRequest)
                    ->andReturn($cookie);

                $getUserInfoStorage            = new stdClass();
                $getUserInfoStorage->callCount = 0;
                $getUserInfoFromSessionId      = Mockery::mock(
                    GetUserInfoFromSessionId::class,
                );
                $getUserInfoFromSessionId->expects('get')
                    ->twice()
                    ->andReturnUsing(function () use (
                        $getUserInfoStorage,
                        $userInfo2,
                    ): OauthUserInfo {
                        $getUserInfoStorage->callCount += 1;

                        if ($getUserInfoStorage->callCount === 1) {
                            return new OauthUserInfo();
                        }

                        return $userInfo2;
                    });

                $refreshAccessTokenBySessionId = Mockery::mock(
                    RefreshAccessTokenBySessionId::class,
                );

                $refreshAccessTokenBySessionId->expects('refresh')
                    ->with('mock-id');

                $container = Mockery::mock(ContainerInterface::class);
                $container->expects('get')
                    ->with(GetUserInfoFromSessionId::class)
                    ->andReturn($getUserInfoFromSessionId);
                $container->expects('get')
                    ->with(RefreshAccessTokenBySessionId::class)
                    ->andReturn($refreshAccessTokenBySessionId);

                $sut = new RxAnteUserInfoRepository(
                    jwtFactory: Mockery::mock(JwtFactory::class),
                    container: $container,
                    fetchUserInfoFactory: Mockery::mock(
                        FetchUserInfoFactory::class,
                    ),
                    sessionTokenCookieHandler: $sessionTokenCookieHandler,
                );

                $result = $sut->getUserInfoFromRequestSession(
                    request: $serverRequest,
                );

                expect($result)->toBe($userInfo2);
            },
        );
    },
);
