// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import { IoRedisRefreshLockFactory } from './IoRedisRefreshLockFactory';

// Mocks
describe('IoRedisRefreshLockFactory', () => {
    let redis: any;

    let lock: ReturnType<typeof IoRedisRefreshLockFactory>;

    beforeEach(() => {
        redis = {
            set: vi.fn(),
            del: vi.fn(),
        };

        vi.mock('sleep-promise', () => ({
            default: () => Promise.resolve(),
        }));

        vi.clearAllMocks();

        lock = IoRedisRefreshLockFactory({ redis });
    });

    it(
        'acquire: should set lock and return when successful',
        async () => {
            redis.set.mockResolvedValueOnce('OK');

            await expect(lock.acquire(
                'token123',
            )).resolves.toBeUndefined();

            expect(redis.set).toHaveBeenCalledWith(
                'refresh_token_lock:token123',
                'true',
                'EX',
                60,
                'NX',
            );
        },
    );

    it(
        'acquire: should retry and throw if lock not acquired',
        async () => {
            redis.set.mockResolvedValue(null);

            await expect(lock.acquire(
                'token123',
            )).rejects.toThrow('Could not acquire lock');

            expect(redis.set).toHaveBeenCalledTimes(65);
        },
    );

    it(
        'release: should delete the lock key',
        async () => {
            redis.del.mockResolvedValue(1);

            await lock.release('token123');

            expect(redis.del).toHaveBeenCalledWith(
                'refresh_token_lock:token123',
            );
        },
    );
});
