import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshLock } from './Lock/RefreshLock';
import { RefreshAccessToken } from './RefreshAccessToken';
import { RefreshAccessTokenFactory } from './RefreshAccessTokenFactory';

export function RefreshAccessTokenWithAuth0Factory (
    {
        tokenRepository,
        refreshLock,
        wellKnownUrl,
        clientId,
        clientSecret,
    }: {
        tokenRepository: TokenRepository;
        refreshLock: RefreshLock;
        wellKnownUrl: string;
        clientId: string;
        clientSecret: string;
    },
): RefreshAccessToken {
    return RefreshAccessTokenFactory({
        tokenRepository,
        refreshLock,
        wellKnownUrl,
        clientId,
        clientSecret,
    });
}
