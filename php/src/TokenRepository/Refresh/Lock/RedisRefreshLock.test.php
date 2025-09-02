<?php

declare(strict_types=1);

use RxAnte\OAuth\TokenRepository\Refresh\Lock\RedisRefreshLock;

describe('RedisRefreshLock', function (): void {
    uses()->group('RedisRefreshLock');

    it(
        'acquires a lock successfully',
        function (): void {
            $redisSpy = Mockery::mock('Redis');

            $redisSpy->shouldReceive('set')
                ->once()
                ->with(
                    'refresh_token_lock:token123',
                    'true',
                    ['EX' => 60, 'NX'],
                )
                ->andReturn(true);

            $lock = new RedisRefreshLock($redisSpy);

            $lock->acquire('token123');

            expect(true)->toBeTrue();
        },
    );

    it(
        'throws if lock cannot be acquired',
        function (): void {
            $redis = Mockery::mock('Redis');

            $redis->shouldReceive('set')
                ->times(65)
                ->andReturn(false);

            $lock = new RedisRefreshLock(
                $redis,
                function (int $seconds): void {
                    expect($seconds)->toBe(1);
                },
            );

            expect(fn () => $lock->acquire('token123'))
                ->toThrow(
                    RuntimeException::class,
                    'Could not acquire lock',
                );
        },
    );

    it(
        'releases a lock',
        function (): void {
            $redis = Mockery::mock('Redis');

            $redis->shouldReceive('del')
                ->once()
                ->with('refresh_token_lock:token123')
                ->andReturn(1);

            $lock = new RedisRefreshLock($redis);

            $lock->release('token123');

            expect(true)->toBeTrue();
        },
    );
});
