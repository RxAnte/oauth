import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import * as crypto from 'crypto';
import Redis from 'ioredis';
import { Account } from 'next-auth';
import { CreateSessionIdWithToken } from './CreateSessionIdWithToken';
import { User } from '../../User';

vi.mock('ioredis');
vi.mock('crypto', () => ({
    randomUUID: vi.fn(),
}));

beforeEach(() => {
    vi.clearAllMocks(); // Reset mock state before each test
});

describe('CreateSessionIdWithToken', () => {
    it(
        'should create a session ID and store the token in Redis',
        async () => {
            const mockRedis = new Redis();

            const mockUUID = 'mock-uuid-123';

            // @ts-expect-error TS2741
            const token: Account = {
                access_token: 'access123',
                expires_at: 1234567890,
                refresh_token: 'refresh123',
                provider: 'mock-provider',
                type: 'oauth',
            };

            const user: User = {
                id: '1',
                name: 'Jane Doe',
                email: 'test@example.com',
                sub: 'user-sub',
            };

            const redisTokenExpireTimeInSeconds = 3600;

            vi.mocked(mockRedis.set).mockResolvedValue('OK');

            // @ts-expect-error TS2345
            vi.mocked(crypto.randomUUID).mockReturnValue(mockUUID);

            const result = await CreateSessionIdWithToken(
                token,
                user,
                mockRedis,
                redisTokenExpireTimeInSeconds,
            );

            expect(crypto.randomUUID).toHaveBeenCalled();

            expect(mockRedis.set).toHaveBeenCalledWith(
                `user_token:${mockUUID}`,
                JSON.stringify({
                    accessToken: token.access_token,
                    accessTokenExpires: token.expires_at,
                    refreshToken: token.refresh_token,
                    user,
                }),
                'EX',
                redisTokenExpireTimeInSeconds,
            );
            expect(result).toBe(mockUUID);
        },
    );

    it(
        'should throw an error if Redis fails to store the token',
        async () => {
            const mockRedis = new Redis();

            const mockUUID = 'mock-uuid-123';

            // @ts-expect-error TS2741
            const token: Account = {
                access_token: 'access123',
                expires_at: 1234567890,
                refresh_token: 'refresh123',
                provider: 'mock-provider',
                type: 'oauth',
            };

            const user: User = {
                id: '1',
                name: 'Jane Doe',
                email: 'test@example.com',
                sub: 'user-sub',
            };

            const redisTokenExpireTimeInSeconds = 3600;

            vi.mocked(mockRedis.set).mockRejectedValue(
                new Error('Redis error'),
            );

            // @ts-expect-error TS2345
            vi.mocked(crypto.randomUUID).mockReturnValue(mockUUID);

            await expect(
                CreateSessionIdWithToken(
                    token,
                    user,
                    mockRedis,
                    redisTokenExpireTimeInSeconds,
                ),
            ).rejects.toThrow('Redis error');

            expect(crypto.randomUUID).toHaveBeenCalled();

            expect(mockRedis.set).toHaveBeenCalledWith(
                `user_token:${mockUUID}`,
                JSON.stringify({
                    accessToken: token.access_token,
                    accessTokenExpires: token.expires_at,
                    refreshToken: token.refresh_token,
                    user,
                }),
                'EX',
                redisTokenExpireTimeInSeconds,
            );
        },
    );
});
