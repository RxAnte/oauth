// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { TokenData } from '../../TokenData';
import { GetIdFromCookies } from './GetIdFromCookies';
import { SetTokenFromSessionId } from './SetTokenFromSessionId';

export async function SetTokenBasedOnCookies (
    token: TokenData,
    redis: Redis,
    redisTokenExpireTimeInSeconds: number,
    /** @deprecated secret is no longer require unless still using next-auth */
    secret?: string,
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
