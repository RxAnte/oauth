import Redis from 'ioredis';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';
export declare function SetTokenFromSessionId(token: NextAuthJwt, sessionId: string, redis: Redis, redisTokenExpireTimeInSeconds: number): Promise<void>;
