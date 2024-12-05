import Redis from 'ioredis';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';
export declare function FindTokenBySessionId(sessionId: string, redis: Redis): Promise<NextAuthJwt | null>;
