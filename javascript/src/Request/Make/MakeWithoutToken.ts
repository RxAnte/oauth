import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { ParseResponse } from './ParseResponse';

async function sendRequest (
    {
        uri,
        method,
        queryParams,
        payload,
        cacheTags,
        cacheSeconds,
    }: RequestProperties,
    requestBaseUrl: string,
) {
    const url = new URL(`${requestBaseUrl}${uri}?${queryParams.toString()}`);

    const headers = new Headers({
        RequestType: 'api',
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Provider: 'auth0',
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

export async function MakeWithoutToken (
    props: RequestProperties,
    requestBaseUrl: string,
): Promise<RequestResponse> {
    return ParseResponse(async () => sendRequest(
        props,
        requestBaseUrl,
    ));
}
