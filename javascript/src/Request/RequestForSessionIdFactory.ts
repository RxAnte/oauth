import { RequestPropertiesOptional } from './RequestProperties';
import { RequestForSessionId } from './RequestForSessionId';
import RequestMethods from './RequestMethods';
import { TokenRepository } from '../TokenRepository/TokenRepository';
import { MakeWithTokenForSessionId } from './Make/MakeWithTokenForSessionId';
import { RefreshAccessTokenForSessionId } from './RefreshAccessToken/RefreshAccessTokenForSessionId';

export function RequestForSessionIdFactory (
    {
        requestBaseUrl,
        tokenRepository,
        nextAuthProviderId,
        refreshAccessToken,
    }: {
        requestBaseUrl: string;
        tokenRepository: TokenRepository;
        /** @deprecated RxAnte Oauth is moving away from next-auth. */
        nextAuthProviderId?: string;
        refreshAccessToken: RefreshAccessTokenForSessionId;
    },
): RequestForSessionId {
    return {
        makeWithToken: async (
            sessionId: string,
            {
                uri = '',
                method = RequestMethods.GET,
                queryParams = new URLSearchParams(),
                payload = {},
                cacheTags = [],
                cacheSeconds = 300,
                headers = {},
            }: RequestPropertiesOptional,
        ) => MakeWithTokenForSessionId(
            {
                uri,
                method,
                queryParams,
                payload,
                cacheTags,
                cacheSeconds,
                headers,
            },
            requestBaseUrl,
            nextAuthProviderId,
            tokenRepository,
            refreshAccessToken,
            sessionId,
        ),
    };
}
