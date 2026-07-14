import { RequestPropertiesOptional } from './RequestProperties';
import { RequestResponse } from './RequestResponse';
export type RequestForSessionId = {
    makeWithToken: (sessionId: string, props: RequestPropertiesOptional) => Promise<RequestResponse>;
};
