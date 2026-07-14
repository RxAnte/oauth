import { RequestForSessionId } from './RequestForSessionId';
import { TokenRepository } from '../TokenRepository/TokenRepository';
import { RefreshAccessTokenForSessionId } from './RefreshAccessToken/RefreshAccessTokenForSessionId';
export declare function RequestForSessionIdFactory({ requestBaseUrl, tokenRepository, nextAuthProviderId, refreshAccessToken, signInUri, }: {
    requestBaseUrl: string;
    tokenRepository: TokenRepository;
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId?: string;
    refreshAccessToken: RefreshAccessTokenForSessionId;
    signInUri?: string;
}): RequestForSessionId;
