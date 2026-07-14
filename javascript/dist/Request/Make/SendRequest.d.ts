import { RequestPropertiesWithToken } from '../RequestProperties';
export declare function SendRequest({ uri, method, queryParams, payload, cacheTags, cacheSeconds, headers: customHeaders, token, }: RequestPropertiesWithToken, requestBaseUrl: string, 
/** @deprecated RxAnte Oauth is moving away from next-auth. */
nextAuthProviderId?: string | undefined): Promise<Response>;
