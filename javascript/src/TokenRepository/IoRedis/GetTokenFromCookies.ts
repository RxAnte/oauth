// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { FindTokenFromCookies } from './FindTokenFromCookies';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';

export async function GetTokenFromCookies (
    redis: Redis,
    secret: string,
): Promise<NextAuthJwt> {
    const token = await FindTokenFromCookies(redis, secret);

    if (!token) {
        throw new Error('Unable to find token');
    }

    return token;
}
