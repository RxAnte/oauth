export default class RequestAuthenticationError extends Error {
    readonly statusCode: number | undefined;
    readonly statusText: string | undefined;
    constructor(statusText: string | undefined);
}
