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
    }: {
        tokenRepository: TokenRepository;
        appUrl: string;
        wellKnownUrl: string;
        clientId: string;
        clientSecret: string;
        callbackUri?: string;
    },
): Promise<AuthCodeGrantApi> {
    const wellKnown = await GetWellKnown(wellKnownUrl);

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
