<?php

declare(strict_types=1);

use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use RxAnte\OAuth\TokenRepository\SetAccessTokenFromSessionId;
use RxAnte\OAuth\TokenRepository\TokenRepositoryConfig;

describe('SetAccessTokenFromSessionId', function (): void {
    uses()->group('SetAccessTokenFromSessionId');

    it('throws if session id is empty', function (): void {
        $accessToken = Mockery::mock(AccessTokenInterface::class);

        $config = new TokenRepositoryConfig(expireInSeconds: 123);

        $setter = new SetAccessTokenFromSessionId(
            Mockery::mock(ClockInterface::class),
            $config,
            Mockery::mock(CacheItemPoolInterface::class),
        );
        expect(fn () => $setter->set('', $accessToken))
            ->toThrow(
                RuntimeException::class,
                '$sessionId must not be empty',
            );
    });

    it(
        'stores access token in cache and returns on success',
        function (): void {
            $sessionId = 'abc123';

            $accessToken = Mockery::mock(AccessTokenInterface::class);

            $config = new TokenRepositoryConfig(expireInSeconds: 3600);

            $now   = new DateTimeImmutable();
            $clock = Mockery::mock(ClockInterface::class);
            $clock->shouldReceive('now')->andReturn($now);

            $cacheItem = Mockery::mock(CacheItemInterface::class);
            $cacheItem->shouldReceive('set')
                ->with($accessToken)
                ->andReturnSelf();
            $cacheItem->shouldReceive('expiresAt')
                ->andReturnUsing(function (
                    DateTimeImmutable $e,
                ) use (
                    $cacheItem,
                    $now,
                ): CacheItemInterface {
                    expect($e->getTimestamp())->toBe(
                        $now->getTimestamp() + 3600,
                    );

                    return $cacheItem;
                });

            $cachePool = Mockery::mock(CacheItemPoolInterface::class);
            $cachePool->shouldReceive('getItem')
                ->with($config->getSessionIdCacheKey(
                    $sessionId,
                ))
                ->andReturn($cacheItem);
            $cachePool->shouldReceive('save')
                ->with($cacheItem)
                ->andReturn(true);

            $sut = new SetAccessTokenFromSessionId(
                clock: $clock,
                config: $config,
                cachePool: $cachePool,
            );

            $sut->set($sessionId, $accessToken);
        },
    );

    it('throws if cache save fails', function (): void {
        $sessionId = 'abc123';

        $accessToken = Mockery::mock(AccessTokenInterface::class);

        $config = new TokenRepositoryConfig(expireInSeconds: 123);

        $now   = new DateTimeImmutable();
        $clock = Mockery::mock(ClockInterface::class);
        $clock->shouldReceive('now')->andReturn($now);

        $cacheItem = Mockery::mock(CacheItemInterface::class);
        $cacheItem->shouldReceive('set')
            ->with($accessToken)
            ->andReturnSelf();
        $cacheItem->shouldReceive('expiresAt')
            ->andReturnUsing(function (
                DateTimeImmutable $e,
            ) use (
                $cacheItem,
                $now,
            ): CacheItemInterface {
                expect($e->getTimestamp())->toBe(
                    $now->getTimestamp() + 123,
                );

                return $cacheItem;
            });

        $cachePool = Mockery::mock(CacheItemPoolInterface::class);
        $cachePool->shouldReceive('getItem')
            ->with($config->getSessionIdCacheKey($sessionId))
            ->andReturn($cacheItem);
        $cachePool->shouldReceive('save')
            ->with($cacheItem)
            ->andReturn(false);

        $sut = new SetAccessTokenFromSessionId(
            clock: $clock,
            config: $config,
            cachePool: $cachePool,
        );

        expect(fn () => $sut->set($sessionId, $accessToken))
            ->toThrow(
                RuntimeException::class,
                'Unable to store access token',
            );
    });
});
