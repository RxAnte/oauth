<?php

declare(strict_types=1);

use Lcobucci\JWT\UnencryptedToken;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheResponse;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheResponseFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoFromRxAnteAuth;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponse;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponseFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponse;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponseWrapper;

describe('FetchUserInfoFromRxAnteAuth', function (): void {
    uses()->group('FetchUserInfoFromRxAnteAuth');

    it(
        'returns invalid user info object when response is unauthorized',
        function (): void {
            $jwtToken = Mockery::mock(UnencryptedToken::class);

            $responseWrapper = new RxAnteResponseWrapper(
                response: new RxAnteResponse(
                    statusCode: 200,
                    body: 'unauthorized',
                ),
            );

            $cacheResponse = Mockery::mock(CacheResponse::class);
            $cacheResponse->expects('cache')
                ->with($jwtToken, $responseWrapper->response);

            $cacheResponseFactory = Mockery::mock(
                CacheResponseFactory::class,
            );
            $cacheResponseFactory->expects('create')
                ->with($responseWrapper)
                ->andReturn($cacheResponse);

            $getResponse = Mockery::mock(GetRxAnteResponse::class);
            $getResponse->expects('get')
                ->with($jwtToken)
                ->andReturn($responseWrapper);

            $getResponseFactory = Mockery::mock(
                GetRxAnteResponseFactory::class,
            );
            $getResponseFactory->expects('create')
                ->with($jwtToken)
                ->andReturn($getResponse);

            $sut = new FetchUserInfoFromRxAnteAuth(
                cacheResponseFactory: $cacheResponseFactory,
                getResponseFactory: $getResponseFactory,
            );

            $result = $sut->fetch(jwt: $jwtToken);

            expect($result->isValid)->toBeFalse();
        },
    );

    it(
        'returns invalid user info object when response is not http 200',
        function (): void {
            $jwtToken = Mockery::mock(UnencryptedToken::class);

            $responseWrapper = new RxAnteResponseWrapper(
                response: new RxAnteResponse(
                    statusCode: 500,
                    body: '',
                ),
            );

            $cacheResponse = Mockery::mock(CacheResponse::class);
            $cacheResponse->expects('cache')
                ->with($jwtToken, $responseWrapper->response);

            $cacheResponseFactory = Mockery::mock(
                CacheResponseFactory::class,
            );
            $cacheResponseFactory->expects('create')
                ->with($responseWrapper)
                ->andReturn($cacheResponse);

            $getResponse = Mockery::mock(GetRxAnteResponse::class);
            $getResponse->expects('get')
                ->with($jwtToken)
                ->andReturn($responseWrapper);

            $getResponseFactory = Mockery::mock(
                GetRxAnteResponseFactory::class,
            );
            $getResponseFactory->expects('create')
                ->with($jwtToken)
                ->andReturn($getResponse);

            $sut = new FetchUserInfoFromRxAnteAuth(
                cacheResponseFactory: $cacheResponseFactory,
                getResponseFactory: $getResponseFactory,
            );

            $result = $sut->fetch(jwt: $jwtToken);

            expect($result->isValid)->toBeFalse();
        },
    );

    it(
        'returns invalid user info object when response json is invalid',
        function (): void {
            $jwtToken = Mockery::mock(UnencryptedToken::class);

            $responseWrapper = new RxAnteResponseWrapper(
                response: new RxAnteResponse(
                    statusCode: 200,
                    body: '',
                ),
            );

            $cacheResponse = Mockery::mock(CacheResponse::class);
            $cacheResponse->expects('cache')
                ->with($jwtToken, $responseWrapper->response);

            $cacheResponseFactory = Mockery::mock(
                CacheResponseFactory::class,
            );
            $cacheResponseFactory->expects('create')
                ->with($responseWrapper)
                ->andReturn($cacheResponse);

            $getResponse = Mockery::mock(GetRxAnteResponse::class);
            $getResponse->expects('get')
                ->with($jwtToken)
                ->andReturn($responseWrapper);

            $getResponseFactory = Mockery::mock(
                GetRxAnteResponseFactory::class,
            );
            $getResponseFactory->expects('create')
                ->with($jwtToken)
                ->andReturn($getResponse);

            $sut = new FetchUserInfoFromRxAnteAuth(
                cacheResponseFactory: $cacheResponseFactory,
                getResponseFactory: $getResponseFactory,
            );

            $result = $sut->fetch(jwt: $jwtToken);

            expect($result->isValid)->toBeFalse();
        },
    );

    it(
        'returns user info object with no roles',
        function (): void {
            $jwtToken = Mockery::mock(UnencryptedToken::class);

            $responseWrapper = new RxAnteResponseWrapper(
                response: new RxAnteResponse(
                    statusCode: 200,
                    body: (string) json_encode([
                        'sub' => 'mock-sub',
                        'email' => 'mock-email',
                        'name' => 'mock-name',
                        'given_name' => 'mock-given-name',
                        'family_name' => 'mock-family-name',
                        'picture' => 'mock-picture',
                        'roles' => 'foo',
                    ]),
                ),
            );

            $cacheResponse = Mockery::mock(CacheResponse::class);
            $cacheResponse->expects('cache')
                ->with($jwtToken, $responseWrapper->response);

            $cacheResponseFactory = Mockery::mock(
                CacheResponseFactory::class,
            );
            $cacheResponseFactory->expects('create')
                ->with($responseWrapper)
                ->andReturn($cacheResponse);

            $getResponse = Mockery::mock(GetRxAnteResponse::class);
            $getResponse->expects('get')
                ->with($jwtToken)
                ->andReturn($responseWrapper);

            $getResponseFactory = Mockery::mock(
                GetRxAnteResponseFactory::class,
            );
            $getResponseFactory->expects('create')
                ->with($jwtToken)
                ->andReturn($getResponse);

            $sut = new FetchUserInfoFromRxAnteAuth(
                cacheResponseFactory: $cacheResponseFactory,
                getResponseFactory: $getResponseFactory,
            );

            $result = $sut->fetch(jwt: $jwtToken);

            expect($result->isValid)->toBeTrue();

            expect($result->sub)->toBe('mock-sub');

            expect($result->email)->toBe('mock-email');

            expect($result->name)->toBe('mock-name');

            expect($result->givenName)->toBe(
                'mock-given-name',
            );

            expect($result->familyName)->toBe(
                'mock-family-name',
            );

            expect($result->picture)->toBe(
                'mock-picture',
            );

            expect($result->roles)->toBe([]);

            expect($result->hasRole('foo'))->toBeFalse();
        },
    );

    it(
        'returns user info object',
        function (): void {
            $jwtToken = Mockery::mock(UnencryptedToken::class);

            $responseWrapper = new RxAnteResponseWrapper(
                response: new RxAnteResponse(
                    statusCode: 200,
                    body: (string) json_encode([
                        'sub' => 'mock-sub',
                        'email' => 'mock-email',
                        'name' => 'mock-name',
                        'given_name' => 'mock-given-name',
                        'family_name' => 'mock-family-name',
                        'picture' => 'mock-picture',
                        'roles' => [
                            'foo',
                            'bar',
                        ],
                    ]),
                ),
            );

            $cacheResponse = Mockery::mock(CacheResponse::class);
            $cacheResponse->expects('cache')
                ->with($jwtToken, $responseWrapper->response);

            $cacheResponseFactory = Mockery::mock(
                CacheResponseFactory::class,
            );
            $cacheResponseFactory->expects('create')
                ->with($responseWrapper)
                ->andReturn($cacheResponse);

            $getResponse = Mockery::mock(GetRxAnteResponse::class);
            $getResponse->expects('get')
                ->with($jwtToken)
                ->andReturn($responseWrapper);

            $getResponseFactory = Mockery::mock(
                GetRxAnteResponseFactory::class,
            );
            $getResponseFactory->expects('create')
                ->with($jwtToken)
                ->andReturn($getResponse);

            $sut = new FetchUserInfoFromRxAnteAuth(
                cacheResponseFactory: $cacheResponseFactory,
                getResponseFactory: $getResponseFactory,
            );

            $result = $sut->fetch(jwt: $jwtToken);

            expect($result->isValid)->toBeTrue();

            expect($result->sub)->toBe('mock-sub');

            expect($result->email)->toBe('mock-email');

            expect($result->name)->toBe('mock-name');

            expect($result->givenName)->toBe(
                'mock-given-name',
            );

            expect($result->familyName)->toBe(
                'mock-family-name',
            );

            expect($result->picture)->toBe(
                'mock-picture',
            );

            expect($result->roles)->toBe([
                'foo',
                'bar',
            ]);

            expect($result->hasRole('foo'))->toBeTrue();

            expect($result->hasRole('bar'))->toBeTrue();

            expect($result->hasRole('baz'))->toBeFalse();
        },
    );
});
