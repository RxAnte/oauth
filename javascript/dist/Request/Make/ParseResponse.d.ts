import { RequestResponse } from '../RequestResponse';
export declare function ParseResponse(runRequest: () => Promise<Response>): Promise<RequestResponse>;
