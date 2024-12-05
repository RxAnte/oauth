// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';
import { GetIdFromCookies } from './GetIdFromCookies';

export async function SetTokenBasedOnCookies (
    token: NextAuthJwt,
    redis: Redis,
    secret: string,
    redisTokenExpireTimeInSeconds: number,
): Promise<void> {
    const sessionId = await GetIdFromCookies(secret);

    if (!sessionId) {
        return;
    }

    await redis.set(
        `user_token:${sessionId}`,
        JSON.stringify(token),
        'EX',
        redisTokenExpireTimeInSeconds,
    );
}
