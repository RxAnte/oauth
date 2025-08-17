import Redis from 'ioredis';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshLock } from './Lock/RefreshLock';
import { RefreshAccessToken } from './RefreshAccessToken';
export declare function RefreshAccessTokenFactory({ tokenRepository, refreshLock, wellKnownUrl, clientId, clientSecret, redis, wellKnownCacheKey, wellKnownCacheExpiresInSeconds, }: {
    tokenRepository: TokenRepository;
    refreshLock: RefreshLock;
    wellKnownUrl: string;
    clientId: string;
    clientSecret: string;
    redis?: Redis;
    wellKnownCacheKey?: string;
    wellKnownCacheExpiresInSeconds?: number;
}): RefreshAccessToken;
