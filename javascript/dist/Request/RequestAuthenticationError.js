"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
class RequestAuthenticationError extends Error {
    constructor(statusText) {
        super('The request encountered an authentication error.');
        this.statusCode = 401;
        this.statusText = statusText;
    }
}
exports.default = RequestAuthenticationError;
