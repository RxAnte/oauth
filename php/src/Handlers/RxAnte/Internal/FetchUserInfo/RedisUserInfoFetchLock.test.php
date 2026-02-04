<?php

/** @noinspection PhpComposerExtensionStubsInspection */
// phpcs:disable Squiz.Arrays.ArrayDeclaration.NoKeySpecified


declare(strict_types=1);

use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\RedisUserInfoFetchLock;

describe('RedisUserInfoFetchLock', function (): void {
    uses()->group('RedisUserInfoFetchLock');

    it(
        'acquires lock successfully on first try',
        function (): void {
            $redis = Mockery::mock(Redis::class);
            $redis->expects('set')
                ->with(
                    'fetch_user_info_lock:test-key',
                    'true',
                    [
                        'EX' => 60,
                        'NX',
                    ],
                )
                ->andReturn(true);

            $sleepCalled = false;
            $sleep       = function (int $seconds) use (&$sleepCalled): void {
                $sleepCalled = true;
            };

            $sut = new RedisUserInfoFetchLock(
                redis: $redis,
                sleep: $sleep,
            );

            $sut->acquire('test-key');

            expect($sleepCalled)->toBeFalse();
        },
    );

    it(
        'retries acquiring lock and eventually succeeds',
        function (): void {
            $redis = Mockery::mock(Redis::class);
            $redis->expects('set')
                ->with(
                    'fetch_user_info_lock:test-key',
                    'true',
                    [
                        'EX' => 60,
                        'NX',
                    ],
                )
                ->times(3)
                ->andReturn(false, false, true);

            $sleepCallCount = 0;
            $sleep          = function (int $seconds) use (&$sleepCallCount): void {
                expect($seconds)->toBe(1);
                $sleepCallCount++;
            };

            $sut = new RedisUserInfoFetchLock(
                redis: $redis,
                sleep: $sleep,
            );

            $sut->acquire('test-key');

            expect($sleepCallCount)->toBe(2);
        },
    );

    it(
        'throws RuntimeException after 65 failed attempts',
        function (): void {
            $redis = Mockery::mock(Redis::class);
            $redis->expects('set')
                ->with(
                    'fetch_user_info_lock:test-key',
                    'true',
                    [
                        'EX' => 60,
                        'NX',
                    ],
                )
                ->times(65)
                ->andReturn(false);

            $sleepCallCount = 0;
            $sleep          = function (int $seconds) use (&$sleepCallCount): void {
                $sleepCallCount++;
            };

            $sut = new RedisUserInfoFetchLock(
                redis: $redis,
                sleep: $sleep,
            );

            expect(fn () => $sut->acquire('test-key'))
                ->toThrow(RuntimeException::class, 'Could not acquire lock');

            expect($sleepCallCount)->toBe(65);
        },
    );

    it(
        'releases lock by deleting redis key',
        function (): void {
            $redis = Mockery::mock(Redis::class);
            $redis->expects('del')
                ->with('fetch_user_info_from_auth0_lock:test-key')
                ->once();

            $sut = new RedisUserInfoFetchLock(redis: $redis);

            $sut->release('test-key');
        },
    );

    it(
        'uses default sleep function when not provided',
        function (): void {
            $redis = Mockery::mock(Redis::class);
            $redis->expects('set')
                ->once()
                ->andReturn(true);

            $sut = new RedisUserInfoFetchLock(redis: $redis);

            $sut->acquire('test-key');
        },
    );
});
