import Redis from 'ioredis';
import { TokenData } from '../../TokenData';
export declare function SetTokenFromSessionId(token: TokenData, sessionId: string, redis: Redis, redisTokenExpireTimeInSeconds: number): Promise<void>;
