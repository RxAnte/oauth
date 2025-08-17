import Redis from 'ioredis';
interface WellKnown {
    authorizationEndpoint: string;
    tokenEndpoint: string;
    userinfoEndpoint: string;
}
export declare function GetWellKnown(wellKnownUrl: string, redis?: Redis, wellKnownCacheKey?: string, wellKnownCacheExpiresInSeconds?: number): Promise<WellKnown>;
export {};
