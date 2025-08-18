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
exports.MakeWithToken = MakeWithToken;
const RequestAuthenticationError_1 = __importDefault(require("../RequestAuthenticationError"));
const AccessDeniedResponse_1 = require("./AccessDeniedResponse");
const ParseResponse_1 = require("./ParseResponse");
function sendRequest(_a, requestBaseUrl_1) {
    return __awaiter(this, arguments, void 0, function* ({ uri, method, queryParams, payload, cacheTags, cacheSeconds, token, }, requestBaseUrl, 
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId = undefined) {
        const { accessToken } = token;
        if (accessToken === null) {
            throw new RequestAuthenticationError_1.default('Could not get access token');
        }
        const url = new URL(`${requestBaseUrl}${uri}?${queryParams.toString()}`);
        const headers = {
            Authorization: `Bearer ${accessToken}`,
            RequestType: 'api',
            Accept: 'application/json',
            'Content-Type': 'application/json',
        };
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
function MakeWithToken(props, requestBaseUrl, 
/** @deprecated RxAnte Oauth is moving away from next-auth. */
nextAuthProviderId, tokenRepository, refreshAccessToken) {
    return __awaiter(this, void 0, void 0, function* () {
        // First check that a token exists, if not, we should bail out
        const token = yield tokenRepository.findTokenFromCookies();
        if (!token) {
            return AccessDeniedResponse_1.AccessDeniedUserNotLoggedInResponse;
        }
        // Now we know we have a token, so we'll make the request
        let response = yield (0, ParseResponse_1.ParseResponse)(() => __awaiter(this, void 0, void 0, function* () {
            return sendRequest(Object.assign(Object.assign({}, props), { token }), requestBaseUrl, nextAuthProviderId);
        }));
        // If there's no authentication issue, return the response, whatever it is
        if (response.status !== 401) {
            return response;
        }
        /**
         * If there is an authentication issue, we'll attempt to refresh the token
         * then make the api request
         */
        let tries = 0;
        do {
            // eslint-disable-next-line no-await-in-loop
            yield refreshAccessToken();
            let newToken = null;
            // eslint-disable-next-line no-await-in-loop
            newToken = yield tokenRepository.findTokenFromCookies();
            if (!newToken) {
                return response;
            }
            // eslint-disable-next-line no-await-in-loop
            response = yield (0, ParseResponse_1.ParseResponse)(() => __awaiter(this, void 0, void 0, function* () {
                return sendRequest(Object.assign(Object.assign({}, props), { token: newToken }), requestBaseUrl, nextAuthProviderId);
            }));
            tries += 1;
        } while (tries < 2 && response.status === 401);
        return response;
    });
}
