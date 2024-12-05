import { RequestPropertiesOptional } from './RequestProperties';
import { RequestResponse } from './RequestResponse';

export type Request = {
    makeWithoutToken: (props: RequestPropertiesOptional) => Promise<RequestResponse>;
    makeWithToken: (props: RequestPropertiesOptional) => Promise<RequestResponse>;
    makeWithSignInRedirect: (props: RequestPropertiesOptional) => Promise<RequestResponse>;
};
