import Redis from 'ioredis';
import { TokenRepository } from '../TokenRepository';
export declare function IoRedisTokenRepositoryFactory({ redis, secret, redisTokenExpireTimeInSeconds, }: {
    redis: Redis;
    secret: string;
    redisTokenExpireTimeInSeconds: number;
}): TokenRepository;
