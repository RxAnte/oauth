import { RequestPropertiesOptional } from './RequestProperties';
import { RequestResponse } from './RequestResponse';
import { PagesRequest } from '../TokenRepository/OAuthSessionIdCookie';

export type PagesRouterRedirect = {
    redirect: {
        destination: string;
        permanent: false;
    };
};

export type RequestForSessionId = {
    makeWithToken: (
        sessionId: string,
        props: RequestPropertiesOptional,
    ) => Promise<RequestResponse>;
    makeWithSignInRedirect: (
        req: PagesRequest,
        props: RequestPropertiesOptional,
    ) => Promise<RequestResponse | PagesRouterRedirect>;
};
