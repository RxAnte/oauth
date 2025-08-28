import Redis from 'ioredis';
import { Account } from 'next-auth';
import {
    describe, it, expect, vi,
} from 'vitest';
import { IoRedisTokenRepositoryFactory } from './IoRedisTokenRepositoryFactory';
import { CreateSessionIdWithToken } from './CreateSessionIdWithToken';
import { FindTokenBySessionId } from './FindTokenBySessionId';
import { FindTokenFromCookies } from './FindTokenFromCookies';
import { GetTokenFromCookies } from './GetTokenFromCookies';
import { SetTokenFromSessionId } from './SetTokenFromSessionId';
import { SetTokenBasedOnCookies } from './SetTokenBasedOnCookies';
import { User } from '../../User';
import { TokenData } from '../../TokenData';

vi.mock('./CreateSessionIdWithToken');
vi.mock('./FindTokenBySessionId');
vi.mock('./FindTokenFromCookies');
vi.mock('./GetTokenFromCookies');
vi.mock('./SetTokenFromSessionId');
vi.mock('./SetTokenBasedOnCookies');

describe('IoRedisTokenRepositoryFactory', () => {
    it('should create a TokenRepository with the correct methods', async () => {
        const mockRedis = new Redis();

        const redisTokenExpireTimeInSeconds = 3600;

        const secret = 'mock-secret';

        const repository = IoRedisTokenRepositoryFactory({
            redis: mockRedis,
            secret,
            redisTokenExpireTimeInSeconds,
        });

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

        const sessionId = 'mock-session-id';

        // @ts-expect-error TS2741
        const tokenData: TokenData = {
            accessToken: 'access123',
            refreshToken: 'refresh123',
            user,
        };

        // Test createSessionIdWithToken
        await repository.createSessionIdWithToken(token, user);
        expect(CreateSessionIdWithToken).toHaveBeenCalledWith(
            token,
            user,
            mockRedis,
            redisTokenExpireTimeInSeconds,
        );

        // Test findTokenBySessionId
        await repository.findTokenBySessionId(sessionId);
        expect(FindTokenBySessionId).toHaveBeenCalledWith(sessionId, mockRedis);

        // Test findTokenFromCookies
        await repository.findTokenFromCookies();
        expect(FindTokenFromCookies).toHaveBeenCalledWith(mockRedis, secret);

        // Test getTokenFromCookies
        await repository.getTokenFromCookies();
        expect(GetTokenFromCookies).toHaveBeenCalledWith(mockRedis, secret);

        // Test setTokenFromSessionId
        await repository.setTokenFromSessionId(tokenData, sessionId);
        expect(SetTokenFromSessionId).toHaveBeenCalledWith(
            tokenData,
            sessionId,
            mockRedis,
            redisTokenExpireTimeInSeconds,
        );

        // Test setTokenBasedOnCookies
        await repository.setTokenBasedOnCookies(tokenData);
        expect(SetTokenBasedOnCookies).toHaveBeenCalledWith(
            tokenData,
            mockRedis,
            redisTokenExpireTimeInSeconds,
            secret,
        );
    });
});
