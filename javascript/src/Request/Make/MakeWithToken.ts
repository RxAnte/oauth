import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { AccessDeniedUserNotLoggedInResponse } from './AccessDeniedResponse';
import { ParseResponse } from './ParseResponse';
import { RefreshAccessToken } from '../RefreshAccessToken/RefreshAccessToken';
import { SendRequest } from './SendRequest';

export async function MakeWithToken (
    props: RequestProperties,
    requestBaseUrl: string,
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId: string | undefined,
    tokenRepository: TokenRepository,
    refreshAccessToken: RefreshAccessToken,
): Promise<RequestResponse> {
    // First check that a token exists, if not, we should bail out
    const token = await tokenRepository.findTokenFromCookies();

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
        await refreshAccessToken();

        let newToken = null;

        // eslint-disable-next-line no-await-in-loop
        newToken = await tokenRepository.findTokenFromCookies();

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
