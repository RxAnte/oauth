// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { Account } from 'next-auth';
import { TokenRepository } from '../TokenRepository';
import { User } from '../../NextAuth/User';
import { CreateSessionIdWithToken } from './CreateSessionIdWithToken';
import { FindTokenBySessionId } from './FindTokenBySessionId';
import { FindTokenFromCookies } from './FindTokenFromCookies';
import { GetTokenFromCookies } from './GetTokenFromCookies';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';
import { SetTokenBasedOnCookies } from './SetTokenBasedOnCookies';
import { SetTokenFromSessionId } from './SetTokenFromSessionId';

export function IoRedisTokenRepositoryFactory (
    {
        redis,
        secret,
        redisTokenExpireTimeInSeconds,
    }: {
        redis: Redis;
        secret: string;
        redisTokenExpireTimeInSeconds: number;
    },
): TokenRepository {
    return {
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
            token: NextAuthJwt,
            sessionId: string,
        ) => SetTokenFromSessionId(
            token,
            sessionId,
            redis,
            redisTokenExpireTimeInSeconds,
        ),
        setTokenBasedOnCookies: async (
            token: NextAuthJwt,
        ) => SetTokenBasedOnCookies(
            token,
            redis,
            secret,
            redisTokenExpireTimeInSeconds,
        ),
    };
}
