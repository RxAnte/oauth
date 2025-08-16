// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { FindTokenFromCookies } from './FindTokenFromCookies';
import { TokenData } from '../../TokenData';

export async function GetTokenFromCookies (
    redis: Redis,
    /** @deprecated secret is no longer require unless still using next-auth */
    secret?: string,
): Promise<TokenData> {
    const token = await FindTokenFromCookies(redis, secret);

    if (!token) {
        throw new Error('Unable to find token');
    }

    return token;
}
