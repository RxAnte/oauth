import Redis from 'ioredis';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';
export declare function SetTokenBasedOnCookies(token: NextAuthJwt, redis: Redis, secret: string, redisTokenExpireTimeInSeconds: number): Promise<void>;
