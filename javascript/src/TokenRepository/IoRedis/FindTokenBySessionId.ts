// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/destructuring-assignment */
// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { TokenData, TokenDataSchema } from '../../TokenData';

export async function FindTokenBySessionId (
    sessionId: string,
    redis: Redis,
): Promise<TokenData | null> {
    const tokenString = await redis.get(`user_token:${sessionId}`);

    if (!tokenString) {
        return null;
    }

    try {
        const token = JSON.parse(tokenString) as TokenData;

        TokenDataSchema.parse(token);

        return token;
    } catch (error) {
        return null;
    }
}
