// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';
import { FindTokenBySessionId } from './FindTokenBySessionId';
import { GetIdFromCookies } from './GetIdFromCookies';

export async function FindTokenFromCookies (
    redis: Redis,
    secret: string,
): Promise<NextAuthJwt | null> {
    const sessionId = await GetIdFromCookies(secret);

    if (!sessionId) {
        return null;
    }

    return FindTokenBySessionId(
        sessionId,
        redis,
    );
}
