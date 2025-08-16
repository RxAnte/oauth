import Redis from 'ioredis';
import { TokenRepository } from '../TokenRepository';
export declare function IoRedisTokenRepositoryFactory({ redis, 
/** @deprecated secret is no longer require unless still using next-auth */
secret, redisTokenExpireTimeInSeconds, }: {
    redis: Redis;
    /** @deprecated secret is no longer require unless still using next-auth */
    secret?: string;
    redisTokenExpireTimeInSeconds: number;
}): TokenRepository;
