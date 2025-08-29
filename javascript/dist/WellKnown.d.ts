import Redis from 'ioredis';
export interface WellKnown {
    authorizationEndpoint: string;
    tokenEndpoint: string;
    userinfoEndpoint: string;
}
export declare function GetWellKnown(wellKnownUrl: string, redis?: Redis, wellKnownCacheKey?: string, wellKnownCacheExpiresInSeconds?: number): Promise<WellKnown>;
