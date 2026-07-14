"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.SendRequest = SendRequest;
const RequestAuthenticationError_1 = __importDefault(require("../RequestAuthenticationError"));
function SendRequest(_a, requestBaseUrl_1) {
    return __awaiter(this, arguments, void 0, function* ({ uri, method, queryParams, payload, cacheTags, cacheSeconds, headers: customHeaders, token, }, requestBaseUrl, 
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId = undefined) {
        const { accessToken } = token;
        if (accessToken === null) {
            throw new RequestAuthenticationError_1.default('Could not get access token');
        }
        const url = new URL(`${requestBaseUrl}${uri}?${queryParams.toString()}`);
        /**
         * Caller supplied headers are merged in first so that the headers this
         * package manages (Authorization, RequestType, Accept, Content-Type and
         * Provider) always take precedence and can never be silently overridden.
         */
        const headers = Object.assign(Object.assign({}, customHeaders), { Authorization: `Bearer ${accessToken}`, RequestType: 'api', Accept: 'application/json', 'Content-Type': 'application/json' });
        if (nextAuthProviderId) {
            headers.Provider = nextAuthProviderId;
        }
        const body = JSON.stringify(payload);
        const options = {
            redirect: 'manual',
            method,
            headers: new Headers(headers),
            next: {
                tags: cacheTags,
                revalidate: cacheSeconds,
            },
        };
        if ((method !== 'HEAD' && method !== 'GET')) {
            options.body = body;
        }
        return fetch(url, options);
    });
}
