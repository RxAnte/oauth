import { Account } from 'next-auth';
import Redis from 'ioredis';
import { User } from '../../User';
/** @deprecated This was used to support next-auth and is no longer used */
export declare function CreateSessionIdWithToken(token: Account, user: User, redis: Redis, redisTokenExpireTimeInSeconds: number): Promise<string>;
