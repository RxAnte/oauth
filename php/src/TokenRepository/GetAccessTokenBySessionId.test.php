<?php

declare(strict_types=1);

use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use RxAnte\OAuth\TokenRepository\EmptyAccessToken;
use RxAnte\OAuth\TokenRepository\GetAccessTokenBySessionId;
use RxAnte\OAuth\TokenRepository\TokenRepositoryConfig;

describe('GetAccessTokenBySessionId', function (): void {
    uses()->group('GetAccessTokenBySessionId');

    it(
        'returns empty access token when cache item is not hit',
        function (): void {
            $cacheItem = Mockery::mock(CacheItemInterface::class);

            $cacheItem->expects('isHit')
                ->andReturnFalse();

            $cachePool = Mockery::mock(CacheItemPoolInterface::class);

            $cachePool->expects('getItem')
                ->with('session_id_user_token-mock-session-id')
                ->andReturn($cacheItem);

            $sut = new GetAccessTokenBySessionId(
                config: new TokenRepositoryConfig(
                    expireInSeconds: 123,
                ),
                cachePool: $cachePool,
            );

            $result = $sut->get('mock-session-id');

            expect($result)->toBeInstanceOf(
                EmptyAccessToken::class,
            );
        },
    );

    it(
        'return access token from cache item',
        function (): void {
            $token = Mockery::mock(AccessTokenInterface::class);

            $cacheItem = Mockery::mock(CacheItemInterface::class);

            $cacheItem->expects('isHit')
                ->andReturnTrue();

            $cacheItem->expects('get')
                ->andReturn($token);

            $cachePool = Mockery::mock(CacheItemPoolInterface::class);

            $cachePool->expects('getItem')
                ->with('foo-prefix_mock-session-id-2')
                ->andReturn($cacheItem);

            $sut = new GetAccessTokenBySessionId(
                config: new TokenRepositoryConfig(
                    expireInSeconds: 123,
                    cacheKeyPrefix: 'foo-prefix_',
                ),
                cachePool: $cachePool,
            );

            $result = $sut->get('mock-session-id-2');

            expect($result)->toBe($token);
        },
    );
});
