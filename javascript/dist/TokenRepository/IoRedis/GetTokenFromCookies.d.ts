import Redis from 'ioredis';
import { TokenData } from '../../TokenData';
export declare function GetTokenFromCookies(redis: Redis, 
/** @deprecated secret is no longer require unless still using next-auth */
secret?: string): Promise<TokenData>;
