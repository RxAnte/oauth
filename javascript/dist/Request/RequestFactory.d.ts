import { Request } from './Request';
import { TokenRepository } from '../TokenRepository/TokenRepository';
import { RefreshAccessToken } from './RefreshAccessToken/RefreshAccessToken';
export declare function RequestFactory({ appUrl, requestBaseUrl, tokenRepository, nextAuthProviderId, refreshAccessToken, }: {
    appUrl: string;
    requestBaseUrl: string;
    tokenRepository: TokenRepository;
    nextAuthProviderId: string;
    refreshAccessToken: RefreshAccessToken;
}): Request;
