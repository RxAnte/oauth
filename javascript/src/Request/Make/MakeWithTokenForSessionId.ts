import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { AccessDeniedUserNotLoggedInResponse } from './AccessDeniedResponse';
import { ParseResponse } from './ParseResponse';
import { RefreshAccessTokenForSessionId } from '../RefreshAccessToken/RefreshAccessTokenForSessionId';
import { SendRequest } from './SendRequest';

export async function MakeWithTokenForSessionId (
    props: RequestProperties,
    requestBaseUrl: string,
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId: string | undefined,
    tokenRepository: TokenRepository,
    refreshAccessToken: RefreshAccessTokenForSessionId,
    sessionId: string,
): Promise<RequestResponse> {
    // First check that a token exists, if not, we should bail out
    const token = await tokenRepository.findTokenBySessionId(sessionId);

    if (!token) {
        return AccessDeniedUserNotLoggedInResponse;
    }

    // Now we know we have a token, so we'll make the request
    let response = await ParseResponse(async () => SendRequest(
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
        await refreshAccessToken(sessionId);

        let newToken = null;

        // eslint-disable-next-line no-await-in-loop
        newToken = await tokenRepository.findTokenBySessionId(sessionId);

        if (!newToken) {
            return response;
        }

        // eslint-disable-next-line no-await-in-loop
        response = await ParseResponse(async () => SendRequest(
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
