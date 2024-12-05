import Redis from 'ioredis';
import { RefreshLock } from './RefreshLock';
export declare function IoRedisRefreshLockFactory({ redis, }: {
    redis: Redis;
}): RefreshLock;
