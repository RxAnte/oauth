import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import Redis from 'ioredis';
import { FindTokenBySessionId } from './FindTokenBySessionId';
import { TokenData, TokenDataSchema } from '../../TokenData';

vi.mock('ioredis');

beforeEach(() => {
    vi.clearAllMocks(); // Reset mock state before each test
});

describe('FindTokenBySessionId', () => {
    it(
        'should return a token when a valid token string is found in Redis',
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

            vi.mocked(mockRedis.get).mockResolvedValue(JSON.stringify(token));

            vi.spyOn(TokenDataSchema, 'parse').mockReturnValue(
                token,
            );

            const result = await FindTokenBySessionId(sessionId, mockRedis);

            expect(mockRedis.get).toHaveBeenCalledWith(
                `user_token:${sessionId}`,
            );

            expect(TokenDataSchema.parse).toHaveBeenCalledWith(token);

            expect(result).toEqual(token);
        },
    );

    it(
        'should return null when no token string is found in Redis',
        async () => {
            const mockRedis = new Redis();

            const sessionId = 'session-123';

            vi.mocked(mockRedis.get).mockResolvedValue(null);

            const result = await FindTokenBySessionId(sessionId, mockRedis);

            expect(mockRedis.get).toHaveBeenCalledWith(
                `user_token:${sessionId}`,
            );

            expect(result).toBeNull();
        },
    );

    it(
        'should return null when the token string is invalid JSON',
        async () => {
            const mockRedis = new Redis();

            const sessionId = 'session-123';

            vi.mocked(mockRedis.get).mockResolvedValue('invalid-json');

            const result = await FindTokenBySessionId(sessionId, mockRedis);

            expect(mockRedis.get).toHaveBeenCalledWith(
                `user_token:${sessionId}`,
            );

            expect(result).toBeNull();
        },
    );

    it(
        'should return null when the token fails schema validation',
        async () => {
            const mockRedis = new Redis();

            const sessionId = 'session-123';

            const invalidToken = { invalid: 'data' };

            vi.mocked(mockRedis.get).mockResolvedValue(JSON.stringify(invalidToken));

            vi.spyOn(
                TokenDataSchema,
                'parse',
            ).mockImplementation(() => {
                throw new Error('Invalid token schema');
            });

            const result = await FindTokenBySessionId(sessionId, mockRedis);

            expect(mockRedis.get).toHaveBeenCalledWith(
                `user_token:${sessionId}`,
            );

            expect(TokenDataSchema.parse).toHaveBeenCalledWith(invalidToken);

            expect(result).toBeNull();
        },
    );
});
