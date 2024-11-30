import Redis from 'ioredis';
import { TokenRepository } from './TokenRepository';
export declare function TokenRepositoryForIoRedisFactory({ redis, redisTokenExpireTimeInSeconds, }: {
    redis: Redis;
    redisTokenExpireTimeInSeconds: number;
}): TokenRepository;
