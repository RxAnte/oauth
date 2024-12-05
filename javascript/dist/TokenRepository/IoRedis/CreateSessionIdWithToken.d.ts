import { Account } from 'next-auth';
import Redis from 'ioredis';
import { User } from '../../NextAuth/User';
export declare function CreateSessionIdWithToken(token: Account, user: User, redis: Redis, redisTokenExpireTimeInSeconds: number): Promise<string>;
