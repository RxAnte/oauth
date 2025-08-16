import Redis from 'ioredis';
import { TokenData } from '../../TokenData';
export declare function FindTokenFromCookies(redis: Redis, 
/** @deprecated secret is no longer require unless still using next-auth */
secret?: string): Promise<TokenData | null>;
