// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { TokenData } from '../../TokenData';
import { FindTokenBySessionId } from './FindTokenBySessionId';
import { GetIdFromCookies } from './GetIdFromCookies';

export async function FindTokenFromCookies (
    redis: Redis,
    /** @deprecated secret is no longer require unless still using next-auth */
    secret?: string,
): Promise<TokenData | null> {
    const sessionId = await GetIdFromCookies(secret);

    if (!sessionId) {
        return null;
    }

    return FindTokenBySessionId(
        sessionId,
        redis,
    );
}
