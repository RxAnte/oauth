import { RequestPropertiesOptional } from './RequestProperties';
import { Request } from './Request';
import { MakeWithoutToken } from './Make/MakeWithoutToken';
import RequestMethods from './RequestMethods';
import { TokenRepository } from '../TokenRepository/TokenRepository';
import { MakeWithToken } from './Make/MakeWithToken';
import { MakeWithSignInRedirect } from './Make/MakeWithSignInRedirect';
import { RefreshAccessToken } from './RefreshAccessToken/RefreshAccessToken';

export function RequestFactory (
    {
        appUrl,
        requestBaseUrl,
        tokenRepository,
        nextAuthProviderId,
        refreshAccessToken,
        signInUri = '/api/auth/sign-in',
    }: {
        appUrl: string;
        requestBaseUrl: string;
        tokenRepository: TokenRepository;
        /** @deprecated RxAnte Oauth is moving away from next-auth. */
        nextAuthProviderId?: string;
        refreshAccessToken: RefreshAccessToken;
        signInUri?: string;
    },
): Request {
    return {
        makeWithoutToken: async (
            {
                uri = '',
                method = RequestMethods.GET,
                queryParams = new URLSearchParams(),
                payload = {},
                cacheTags = [],
                cacheSeconds = 300,
            }: RequestPropertiesOptional,
        ) => MakeWithoutToken(
            {
                uri,
                method,
                queryParams,
                payload,
                cacheTags,
                cacheSeconds,
            },
            requestBaseUrl,
        ),
        makeWithToken: async (
            {
                uri = '',
                method = RequestMethods.GET,
                queryParams = new URLSearchParams(),
                payload = {},
                cacheTags = [],
                cacheSeconds = 300,
            }: RequestPropertiesOptional,
        ) => MakeWithToken(
            {
                uri,
                method,
                queryParams,
                payload,
                cacheTags,
                cacheSeconds,
            },
            requestBaseUrl,
            nextAuthProviderId,
            tokenRepository,
            refreshAccessToken,
        ),
        makeWithSignInRedirect: async (
            {
                uri = '',
                method = RequestMethods.GET,
                queryParams = new URLSearchParams(),
                payload = {},
                cacheTags = [],
                cacheSeconds = 300,
            }: RequestPropertiesOptional,
        ) => MakeWithSignInRedirect(
            {
                uri,
                method,
                queryParams,
                payload,
                cacheTags,
                cacheSeconds,
            },
            appUrl,
            requestBaseUrl,
            nextAuthProviderId,
            tokenRepository,
            refreshAccessToken,
            signInUri,
        ),
    };
}
