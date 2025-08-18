import { Request } from './Request';
import { TokenRepository } from '../TokenRepository/TokenRepository';
import { RefreshAccessToken } from './RefreshAccessToken/RefreshAccessToken';
export declare function RequestFactory({ appUrl, requestBaseUrl, tokenRepository, nextAuthProviderId, refreshAccessToken, signInUri, }: {
    appUrl: string;
    requestBaseUrl: string;
    tokenRepository: TokenRepository;
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId?: string;
    refreshAccessToken: RefreshAccessToken;
    signInUri?: string;
}): Request;
