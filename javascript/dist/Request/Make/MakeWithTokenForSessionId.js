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
Object.defineProperty(exports, "__esModule", { value: true });
exports.MakeWithTokenForSessionId = MakeWithTokenForSessionId;
const AccessDeniedResponse_1 = require("./AccessDeniedResponse");
const ParseResponse_1 = require("./ParseResponse");
const SendRequest_1 = require("./SendRequest");
function MakeWithTokenForSessionId(props, requestBaseUrl, 
/** @deprecated RxAnte Oauth is moving away from next-auth. */
nextAuthProviderId, tokenRepository, refreshAccessToken, sessionId) {
    return __awaiter(this, void 0, void 0, function* () {
        // First check that a token exists, if not, we should bail out
        const token = yield tokenRepository.findTokenBySessionId(sessionId);
        if (!token) {
            return AccessDeniedResponse_1.AccessDeniedUserNotLoggedInResponse;
        }
        // Now we know we have a token, so we'll make the request
        let response = yield (0, ParseResponse_1.ParseResponse)(() => __awaiter(this, void 0, void 0, function* () {
            return (0, SendRequest_1.SendRequest)(Object.assign(Object.assign({}, props), { token }), requestBaseUrl, nextAuthProviderId);
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
            yield refreshAccessToken(sessionId);
            let newToken = null;
            // eslint-disable-next-line no-await-in-loop
            newToken = yield tokenRepository.findTokenBySessionId(sessionId);
            if (!newToken) {
                return response;
            }
            // eslint-disable-next-line no-await-in-loop
            response = yield (0, ParseResponse_1.ParseResponse)(() => __awaiter(this, void 0, void 0, function* () {
                return (0, SendRequest_1.SendRequest)(Object.assign(Object.assign({}, props), { token: newToken }), requestBaseUrl, nextAuthProviderId);
            }));
            tries += 1;
        } while (tries < 2 && response.status === 401);
        return response;
    });
}
