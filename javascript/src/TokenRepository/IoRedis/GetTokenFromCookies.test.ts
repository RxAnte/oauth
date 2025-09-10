import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import Redis from 'ioredis';
import { GetTokenFromCookies } from './GetTokenFromCookies';
import { FindTokenFromCookies } from './FindTokenFromCookies';
import type { TokenData } from '../../TokenData';

vi.mock('ioredis');
vi.mock('./FindTokenFromCookies');

beforeEach(() => {
    vi.clearAllMocks();
});

describe('GetTokenFromCookies', () => {
    it(
        'should return a token when FindTokenFromCookies resolves with a token',
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

            vi.mocked(FindTokenFromCookies).mockResolvedValue(token);

            const result = await GetTokenFromCookies(
                mockRedis,
                'foo-secret',
            );

            expect(FindTokenFromCookies).toHaveBeenCalledWith(
                mockRedis,
                'foo-secret',
            );

            expect(result).toEqual(token);
        },
    );

    it(
        'should throw an error when FindTokenFromCookies resolves with null',
        async () => {
            const mockRedis = new Redis();

            vi.mocked(FindTokenFromCookies).mockResolvedValue(null);

            await expect(GetTokenFromCookies(
                mockRedis,
                'foo-secret',
            )).rejects.toThrow(
                'Unable to find token',
            );

            expect(FindTokenFromCookies).toHaveBeenCalledWith(
                mockRedis,
                'foo-secret',
            );
        },
    );
});
