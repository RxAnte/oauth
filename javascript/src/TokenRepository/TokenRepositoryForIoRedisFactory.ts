// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { Account } from 'next-auth';
import { randomUUID } from 'crypto';
import { TokenRepository } from './TokenRepository';
import { User } from '../NextAuth/User';

export function TokenRepositoryForIoRedisFactory (
    {
        redis,
        redisTokenExpireTimeInSeconds,
    }: {
        redis: Redis;
        redisTokenExpireTimeInSeconds: number;
    },
): TokenRepository {
    return {
        createSessionIdWithAccessToken: async (token: Account, user: User) => {
            const id = randomUUID();

            await redis.set(
                `user_token:${id}`,
                JSON.stringify({
                    accessToken: token.access_token,
                    accessTokenExpires: token.expires_at,
                    refreshToken: token.refresh_token,
                    user,
                }),
                'EX',
                redisTokenExpireTimeInSeconds,
            );

            return id;
        },
    };
}
