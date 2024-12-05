export default class RequestAuthenticationError extends Error {
    readonly statusCode: number | undefined;

    readonly statusText: string | undefined;

    constructor (
        statusText: string | undefined,
    ) {
        super('The request encountered an authentication error.');

        this.statusCode = 401;

        this.statusText = statusText;
    }
}
