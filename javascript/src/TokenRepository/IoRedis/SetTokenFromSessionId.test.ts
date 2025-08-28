import {
    describe, it, expect, vi,
} from 'vitest';
import { SetTokenFromSessionId } from './SetTokenFromSessionId';
import type { TokenData } from '../../TokenData';

function makeMockRedis () {
    return {
        set: vi.fn().mockResolvedValue('OK'),
    };
}

describe('SetTokenFromSessionId', () => {
    it('should call redis.set with the correct arguments', async () => {
        const redisSpy = makeMockRedis();

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

        const sessionId = 'session-xyz';
        const expireTime = 3600;

        await SetTokenFromSessionId(
            token,
            sessionId,
            redisSpy as never,
            expireTime,
        );

        expect(redisSpy.set).toHaveBeenCalledTimes(1);

        expect(redisSpy.set).toHaveBeenCalledWith(
            `user_token:${sessionId}`,
            JSON.stringify(token),
            'EX',
            expireTime,
        );
    });

    it('should propagate errors from redis.set', async () => {
        const redisSpy = {
            set: vi.fn().mockRejectedValue(new Error('Redis failed')),
        };

        const token = {
            accessToken: 'abc',
            accessTokenExpires: 123,
            refreshToken: 'refresh',
            user: {
                id: '1',
                sub: 's',
                email: 'a@b.com',
                name: 'test',
            },
        };

        await expect(
            SetTokenFromSessionId(
                token as TokenData,
                'sid',
                redisSpy as never,
                10,
            ),
        ).rejects.toThrow('Redis failed');
    });
});
