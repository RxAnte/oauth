import { Account } from 'next-auth';
import { randomUUID } from 'crypto';
// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { User } from '../../User';

/** @deprecated This was used to support next-auth and is no longer used */
export async function CreateSessionIdWithToken (
    token: Account,
    user: User,
    redis: Redis,
    redisTokenExpireTimeInSeconds: number,
): Promise<string> {
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
}
