import Redis from 'ioredis';
import { AuthCodeGrantApi } from './AuthCodeGrantApi';
import { TokenRepository } from '../TokenRepository/TokenRepository';
export declare function WellKnownAuthCodeGrantApiFactory({ tokenRepository, appUrl, wellKnownUrl, clientId, clientSecret, callbackUri, redis, wellKnownCacheKey, wellKnownCacheExpiresInSeconds, // cache for 1 day by default
audience, }: {
    tokenRepository: TokenRepository;
    appUrl: string;
    wellKnownUrl: string;
    clientId: string;
    clientSecret: string;
    callbackUri?: string;
    redis?: Redis;
    wellKnownCacheKey?: string;
    wellKnownCacheExpiresInSeconds?: number;
    audience?: string;
}): Promise<AuthCodeGrantApi>;
