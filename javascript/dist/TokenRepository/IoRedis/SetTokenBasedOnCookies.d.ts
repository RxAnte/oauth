import Redis from 'ioredis';
import { TokenData } from '../../TokenData';
export declare function SetTokenBasedOnCookies(token: TokenData, redis: Redis, redisTokenExpireTimeInSeconds: number, 
/** @deprecated secret is no longer require unless still using next-auth */
secret?: string): Promise<void>;
