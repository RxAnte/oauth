import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { PagesRequest, SessionIdFromPagesRequest } from '../../TokenRepository/OAuthSessionIdCookie';
import { RefreshAccessTokenForSessionId } from '../RefreshAccessToken/RefreshAccessTokenForSessionId';
import { PagesRouterRedirect } from '../RequestForSessionId';
import { MakeWithTokenForSessionId } from './MakeWithTokenForSessionId';

export async function MakeWithSignInRedirectForSessionId (
    props: RequestProperties,
    req: PagesRequest,
    requestBaseUrl: string,
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId: string | undefined,
    tokenRepository: TokenRepository,
    refreshAccessToken: RefreshAccessTokenForSessionId,
    signInUri: string = '/api/auth/sign-in',
): Promise<RequestResponse | PagesRouterRedirect> {
    const sessionId = SessionIdFromPagesRequest(req);

    if (!sessionId) {
        return {
            redirect: {
                destination: signInUri,
                permanent: false,
            },
        };
    }

    const response = await MakeWithTokenForSessionId(
        props,
        requestBaseUrl,
        nextAuthProviderId,
        tokenRepository,
        refreshAccessToken,
        sessionId,
    );

    if (response.status !== 401) {
        return response;
    }

    return {
        redirect: {
            destination: signInUri,
            permanent: false,
        },
    };
}
