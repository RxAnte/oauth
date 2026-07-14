import { RequestPropertiesWithToken } from '../RequestProperties';
import RequestAuthenticationError from '../RequestAuthenticationError';

export async function SendRequest (
    {
        uri,
        method,
        queryParams,
        payload,
        cacheTags,
        cacheSeconds,
        headers: customHeaders,
        token,
    }: RequestPropertiesWithToken,
    requestBaseUrl: string,
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId: string | undefined = undefined,
) {
    const { accessToken } = token;

    if (accessToken === null) {
        throw new RequestAuthenticationError(
            'Could not get access token',
        );
    }

    const url = new URL(`${requestBaseUrl}${uri}?${queryParams.toString()}`);

    /**
     * Caller supplied headers are merged in first so that the headers this
     * package manages (Authorization, RequestType, Accept, Content-Type and
     * Provider) always take precedence and can never be silently overridden.
     */
    const headers: Record<string, string> = {
        ...customHeaders,
        Authorization: `Bearer ${accessToken}`,
        RequestType: 'api',
        Accept: 'application/json',
        'Content-Type': 'application/json',
    };

    if (nextAuthProviderId) {
        headers.Provider = nextAuthProviderId;
    }

    const body = JSON.stringify(payload);

    const options = {
        redirect: 'manual',
        method,
        headers: new Headers(headers),
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
