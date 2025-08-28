import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import Redis from 'ioredis';
import { SetTokenBasedOnCookies } from './SetTokenBasedOnCookies';
import { GetIdFromCookies } from './GetIdFromCookies';
import { SetTokenFromSessionId } from './SetTokenFromSessionId';
import type { TokenData } from '../../TokenData';

vi.mock('./GetIdFromCookies');
vi.mock('./SetTokenFromSessionId');

beforeEach(() => {
    vi.clearAllMocks();
});

describe('SetTokenBasedOnCookies', () => {
    it(
        'should call SetTokenFromSessionId with the correct arguments when sessionId is retrieved',
        async () => {
            const mockRedis = new Redis();

            const token: TokenData = {
                accessToken: 'abc123',
                accessTokenExpires: 1234567890,
                refreshToken: 'refresh123',
                user: {
                    id: '1',
                    sub: 'sub-1',
                    email: 'test@example.com',
                    name: 'Jane Doe',
                },
            };

            const redisTokenExpireTimeInSeconds = 3600;

            const sessionId = 'session-xyz';

            vi.mocked(GetIdFromCookies).mockResolvedValue(sessionId);

            const setTokenSpy = vi.mocked(SetTokenFromSessionId).mockResolvedValue();

            await SetTokenBasedOnCookies(
                token,
                mockRedis,
                redisTokenExpireTimeInSeconds,
                'foo-secret',
            );

            expect(GetIdFromCookies).toHaveBeenCalledWith('foo-secret');

            expect(setTokenSpy).toHaveBeenCalledWith(
                token,
                sessionId,
                mockRedis,
                redisTokenExpireTimeInSeconds,
            );
        },
    );

    it(
        'should not call SetTokenFromSessionId if sessionId is not retrieved',
        async () => {
            const mockRedis = new Redis();

            const token: TokenData = {
                accessToken: 'abc123',
                accessTokenExpires: 1234567890,
                refreshToken: 'refresh123',
                user: {
                    id: '1',
                    sub: 'sub-1',
                    email: 'test@example.com',
                    name: 'Jane Doe',
                },
            };

            const redisTokenExpireTimeInSeconds = 3600;

            vi.mocked(GetIdFromCookies).mockResolvedValue(null);

            const setTokenSpy = vi.mocked(SetTokenFromSessionId).mockResolvedValue();

            await SetTokenBasedOnCookies(
                token,
                mockRedis,
                redisTokenExpireTimeInSeconds,
            );

            expect(GetIdFromCookies).toHaveBeenCalledWith(undefined);

            expect(setTokenSpy).not.toHaveBeenCalled();
        },
    );
});
