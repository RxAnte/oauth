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
    signInUri: string = '/api/auth/sign-in',
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

    const headersCollection = await headers();

    const uri = headersCollection.get('middleware-pathname') || '/';

    let authReturn = appUrl + uri;

    const authReturnQueryString = headersCollection.get('middleware-search-params') || '';

    if (authReturnQueryString) {
        authReturn += `?${authReturnQueryString}`;
    }

    const queryString = new URLSearchParams({
        authReturn: encodeURI(authReturn),
    });

    redirect(`${appUrl}${signInUri}?${queryString.toString()}`);
}
