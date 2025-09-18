// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { Account } from 'next-auth';
import { TokenRepository } from '../TokenRepository';
import { User } from '../../User';
import { CreateSessionIdWithToken } from './CreateSessionIdWithToken';
import { FindTokenBySessionId } from './FindTokenBySessionId';
import { FindTokenFromCookies } from './FindTokenFromCookies';
import { GetTokenFromCookies } from './GetTokenFromCookies';
import { TokenData } from '../../TokenData';
import { SetTokenBasedOnCookies } from './SetTokenBasedOnCookies';
import { SetTokenFromSessionId } from './SetTokenFromSessionId';
import DeleteTokenBySessionId from './DeleteTokenBySessionId';
import DeleteTokenFromCookies from './DeleteTokenFromCookies';

export function IoRedisTokenRepositoryFactory (
    {
        redis,
        /** @deprecated secret is no longer require unless still using next-auth */
        secret,
        redisTokenExpireTimeInSeconds,
    }: {
        redis: Redis;
        /** @deprecated secret is no longer require unless still using next-auth */
        secret?: string;
        redisTokenExpireTimeInSeconds: number;
    },
): TokenRepository {
    return {
        /** @deprecated This was used to support next-auth and is no longer used */
        createSessionIdWithToken: async (
            token: Account,
            user: User,
        ) => CreateSessionIdWithToken(
            token,
            user,
            redis,
            redisTokenExpireTimeInSeconds,
        ),
        findTokenBySessionId: async (
            sessionId: string,
        ) => FindTokenBySessionId(
            sessionId,
            redis,
        ),
        findTokenFromCookies: async () => FindTokenFromCookies(
            redis,
            secret,
        ),
        getTokenFromCookies: async () => GetTokenFromCookies(
            redis,
            secret,
        ),
        setTokenFromSessionId: async (
            token: TokenData,
            sessionId: string,
        ) => SetTokenFromSessionId(
            token,
            sessionId,
            redis,
            redisTokenExpireTimeInSeconds,
        ),
        setTokenBasedOnCookies: async (
            token: TokenData,
        ) => SetTokenBasedOnCookies(
            token,
            redis,
            redisTokenExpireTimeInSeconds,
            secret,
        ),
        deleteTokenBySessionId: async (
            sessionId: string,
        ) => DeleteTokenBySessionId(
            sessionId,
            redis,
        ),
        deleteTokenFromCookies: async () => DeleteTokenFromCookies(
            redis,
        ),
    };
}
