import Redis from 'ioredis';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';
export declare function FindTokenFromCookies(redis: Redis, secret: string): Promise<NextAuthJwt | null>;
