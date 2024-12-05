import Redis from 'ioredis';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';
export declare function GetTokenFromCookies(redis: Redis, secret: string): Promise<NextAuthJwt>;
