// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';
import { GetIdFromCookies } from './GetIdFromCookies';
import { SetTokenFromSessionId } from './SetTokenFromSessionId';

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

    await SetTokenFromSessionId(
        token,
        sessionId,
        redis,
        redisTokenExpireTimeInSeconds,
    );
}
