// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { AuthCodeGrantApi } from './AuthCodeGrantApi';
import { TokenRepository } from '../TokenRepository/TokenRepository';
import { GetWellKnown } from '../WellKnown';
import { AuthCodeGrantApiFactory } from './AuthCodeGrantApiFactory';

export async function WellKnownAuthCodeGrantApiFactory (
    {
        tokenRepository,
        appUrl,
        wellKnownUrl,
        clientId,
        clientSecret,
        callbackUri = '/api/auth/callback',
        redis,
        wellKnownCacheKey = 'rxante_oauth_well_known',
        wellKnownCacheExpiresInSeconds = 86400, // cache for 1 day by default
    }: {
        tokenRepository: TokenRepository;
        appUrl: string;
        wellKnownUrl: string;
        clientId: string;
        clientSecret: string;
        callbackUri?: string;
        redis?: Redis;
        wellKnownCacheKey?: string;
        wellKnownCacheExpiresInSeconds?: number;
    },
): Promise<AuthCodeGrantApi> {
    const wellKnown = await GetWellKnown(
        wellKnownUrl,
        redis,
        wellKnownCacheKey,
        wellKnownCacheExpiresInSeconds,
    );

    return AuthCodeGrantApiFactory({
        tokenRepository,
        appUrl,
        authorizeUrl: wellKnown.authorizationEndpoint,
        tokenUrl: wellKnown.tokenEndpoint,
        userInfoUrl: wellKnown.userinfoEndpoint,
        clientId,
        clientSecret,
        callbackUri,
    });
}
