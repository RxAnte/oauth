// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';

export async function SetTokenFromSessionId (
    token: NextAuthJwt,
    sessionId: string,
    redis: Redis,
    redisTokenExpireTimeInSeconds: number,
): Promise<void> {
    await redis.set(
        `user_token:${sessionId}`,
        JSON.stringify(token),
        'EX',
        redisTokenExpireTimeInSeconds,
    );
}
