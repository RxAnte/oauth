import { redirect } from 'next/navigation';
import { headers } from 'next/headers';
import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshAccessToken } from '../RefreshAccessToken/RefreshAccessToken';
import { MakeWithToken } from './MakeWithToken';

export async function MakeWithSignInRedirect (
    props: RequestProperties,
    appUrl: string,
    requestBaseUrl: string,
    nextAuthProviderId: string,
    tokenRepository: TokenRepository,
    refreshAccessToken: RefreshAccessToken,
): Promise<RequestResponse> {
    const response = await MakeWithToken(
        props,
        requestBaseUrl,
        nextAuthProviderId,
        tokenRepository,
        refreshAccessToken,
    );

    if (response.status !== 401) {
        return response;
    }

    const uri = headers().get('middleware-pathname') || '/';

    let authReturn = appUrl + uri;

    const authReturnQueryString = headers().get('middleware-search-params') || '';

    if (authReturnQueryString) {
        authReturn += `?${authReturnQueryString}`;
    }

    const queryString = new URLSearchParams({
        authReturn: encodeURI(authReturn),
    });

    redirect(`${appUrl}/api/auth/sign-in?${queryString.toString()}`);
}
