import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import Redis from 'ioredis';
import { FindTokenFromCookies } from './FindTokenFromCookies';
import { GetIdFromCookies } from './GetIdFromCookies';
import { FindTokenBySessionId } from './FindTokenBySessionId';
import type { TokenData } from '../../TokenData';

vi.mock('./GetIdFromCookies');
vi.mock('./FindTokenBySessionId');

beforeEach(() => {
    vi.clearAllMocks(); // Reset mock state before each test
});

describe('FindTokenFromCookies', () => {
    it(
        'should return a token when sessionId is found and token exists',
        async () => {
            const mockRedis = new Redis();

            const sessionId = 'session-123';

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

            vi.mocked(GetIdFromCookies).mockResolvedValue(sessionId);
            vi.mocked(FindTokenBySessionId).mockResolvedValue(token);

            const result = await FindTokenFromCookies(
                mockRedis,
                'secret-key',
            );

            expect(GetIdFromCookies).toHaveBeenCalledWith('secret-key');

            expect(FindTokenBySessionId).toHaveBeenCalledWith(
                sessionId,
                mockRedis,
            );

            expect(result).toEqual(token);
        },
    );

    it(
        'should return null when sessionId is not found',
        async () => {
            const mockRedis = new Redis();

            vi.mocked(GetIdFromCookies).mockResolvedValue(null);

            const result = await FindTokenFromCookies(
                mockRedis,
                'secret-key',
            );

            expect(GetIdFromCookies).toHaveBeenCalledWith('secret-key');

            expect(FindTokenBySessionId).not.toHaveBeenCalled();

            expect(result).toBeNull();
        },
    );

    it(
        'should return null when no token is found for the sessionId',
        async () => {
            const mockRedis = new Redis();
            const sessionId = 'session-123';

            vi.mocked(GetIdFromCookies).mockResolvedValue(sessionId);
            vi.mocked(FindTokenBySessionId).mockResolvedValue(null);

            const result = await FindTokenFromCookies(
                mockRedis,
                'secret-key',
            );

            expect(GetIdFromCookies).toHaveBeenCalledWith('secret-key');

            expect(FindTokenBySessionId).toHaveBeenCalledWith(
                sessionId,
                mockRedis,
            );

            expect(result).toBeNull();
        },
    );
});
