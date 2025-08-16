import RequestMethods from './RequestMethods';
import { TokenData } from '../TokenData';
export type RequestProperties = {
    uri: string;
    method: RequestMethods;
    queryParams: URLSearchParams;
    payload: Record<never, never>;
    cacheTags: Array<string>;
    cacheSeconds: number | false;
};
export type RequestPropertiesOptional = {
    uri?: string;
    method?: RequestMethods;
    queryParams?: URLSearchParams;
    payload?: Record<never, never>;
    cacheTags?: Array<string>;
    cacheSeconds?: number | false;
};
export type RequestPropertiesWithToken = RequestProperties & {
    token: TokenData;
};
