// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { z } from 'zod';
import MD5 from './MD5';

const WellKnownSchema = z.object({
    authorization_endpoint: z.string(),
    token_endpoint: z.string(),
    userinfo_endpoint: z.string(),
});

export interface WellKnown {
    authorizationEndpoint: string;
    tokenEndpoint: string;
    userinfoEndpoint: string;
}

export async function GetWellKnown (
    wellKnownUrl: string,
    redis?: Redis,
    wellKnownCacheKey: string = 'rxante_oauth_well_known',
    wellKnownCacheExpiresInSeconds: number = 86400, // cache for 1 day by default
): Promise<WellKnown> {
    const cacheKey = `${wellKnownCacheKey}_${MD5(wellKnownUrl)}`;

    if (redis) {
        const redisStore = await redis.get(cacheKey);

        if (redisStore) {
            return JSON.parse(redisStore) as WellKnown;
        }
    }

    const response = await fetch(wellKnownUrl);

    const wellKnownJson = WellKnownSchema.parse(await response.json());

    const wellKnown: WellKnown = {
        authorizationEndpoint: wellKnownJson.authorization_endpoint,
        tokenEndpoint: wellKnownJson.token_endpoint,
        userinfoEndpoint: wellKnownJson.userinfo_endpoint,
    };

    if (redis) {
        await redis.set(
            cacheKey,
            JSON.stringify(wellKnown),
            'EX',
            wellKnownCacheExpiresInSeconds,
        );
    }

    return wellKnown;
}
