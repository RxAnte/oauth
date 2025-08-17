import { z } from 'zod';

const WellKnownSchema = z.object({
    authorization_endpoint: z.string(),
    token_endpoint: z.string(),
    userinfo_endpoint: z.string(),
});

interface WellKnown {
    authorizationEndpoint: string;
    tokenEndpoint: string;
    userinfoEndpoint: string;
}

export async function GetWellKnown (wellKnownUrl: string): Promise<WellKnown> {
    const response = await fetch(wellKnownUrl, {
        cache: 'force-cache',
        // @ts-expect-error TS2769
        cacheSeconds: 86400, // cache for 1 day
    });

    const wellKnownJson = WellKnownSchema.parse(await response.json());

    return {
        authorizationEndpoint: wellKnownJson.authorization_endpoint,
        tokenEndpoint: wellKnownJson.token_endpoint,
        userinfoEndpoint: wellKnownJson.userinfo_endpoint,
    };
}
