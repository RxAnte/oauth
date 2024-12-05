import { RequestResponse } from '../RequestResponse';
import { RequestProperties, RequestPropertiesWithToken } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import RequestAuthenticationError from '../RequestAuthenticationError';
import { AccessDeniedUserNotLoggedInResponse } from './AccessDeniedResponse';
import { ParseResponse } from './ParseResponse';
import { RefreshAccessToken } from '../RefreshAccessToken/RefreshAccessToken';

async function sendRequest (
    {
        uri,
        method,
        queryParams,
        payload,
        cacheTags,
        cacheSeconds,
        token,
    }: RequestPropertiesWithToken,
    requestBaseUrl: string,
    nextAuthProviderId: string,
) {
    const { accessToken } = token;

    if (accessToken === null) {
        throw new RequestAuthenticationError(
            'Could not get access token',
        );
    }

    const url = new URL(`${requestBaseUrl}${uri}?${queryParams.toString()}`);

    const headers = new Headers({
        Authorization: `Bearer ${accessToken}`,
        RequestType: 'api',
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Provider: nextAuthProviderId,
    });

    const body = JSON.stringify(payload);

    const options = {
        redirect: 'manual',
        method,
        headers,
        next: {
            tags: cacheTags,
            revalidate: cacheSeconds,
        },
    } as RequestInit;

    if ((method !== 'HEAD' && method !== 'GET')) {
        options.body = body;
    }

    return fetch(url, options);
}

export async function MakeWithToken (
    props: RequestProperties,
    requestBaseUrl: string,
    nextAuthProviderId: string,
    tokenRepository: TokenRepository,
    refreshAccessToken: RefreshAccessToken,
): Promise<RequestResponse> {
    // First check that a token exists, if not, we should bail out
    const token = await tokenRepository.findTokenFromCookies();

    if (!token) {
        return AccessDeniedUserNotLoggedInResponse;
    }

    // Now we know we have a token, so we'll make the request
    let response = await ParseResponse(async () => sendRequest(
        {
            ...props,
            token,
        },
        requestBaseUrl,
        nextAuthProviderId,
    ));

    // If there's no authentication issue, return the response, whatever it is
    if (response.status !== 401) {
        return response;
    }

    /**
     * If there is an authentication issue, we'll attempt to refresh the token
     * then make the api request
     */

    let tries = 0;

    do {
        // eslint-disable-next-line no-await-in-loop
        await refreshAccessToken();

        let newToken = null;

        // eslint-disable-next-line no-await-in-loop
        newToken = await tokenRepository.findTokenFromCookies();

        if (!newToken) {
            return response;
        }

        // eslint-disable-next-line no-await-in-loop
        response = await ParseResponse(async () => sendRequest(
            {
                ...props,
                token: newToken,
            },
            requestBaseUrl,
            nextAuthProviderId,
        ));

        tries += 1;
    } while (tries < 2 && response.status === 401);

    return response;
}
