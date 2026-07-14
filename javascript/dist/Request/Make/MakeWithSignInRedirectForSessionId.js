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
exports.MakeWithSignInRedirectForSessionId = MakeWithSignInRedirectForSessionId;
const OAuthSessionIdCookie_1 = require("../../TokenRepository/OAuthSessionIdCookie");
const MakeWithTokenForSessionId_1 = require("./MakeWithTokenForSessionId");
function MakeWithSignInRedirectForSessionId(props_1, req_1, requestBaseUrl_1, nextAuthProviderId_1, tokenRepository_1, refreshAccessToken_1) {
    return __awaiter(this, arguments, void 0, function* (props, req, requestBaseUrl, 
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId, tokenRepository, refreshAccessToken, signInUri = '/api/auth/sign-in') {
        const sessionId = (0, OAuthSessionIdCookie_1.SessionIdFromPagesRequest)(req);
        if (!sessionId) {
            return {
                redirect: {
                    destination: signInUri,
                    permanent: false,
                },
            };
        }
        const response = yield (0, MakeWithTokenForSessionId_1.MakeWithTokenForSessionId)(props, requestBaseUrl, nextAuthProviderId, tokenRepository, refreshAccessToken, sessionId);
        if (response.status !== 401) {
            return response;
        }
        return {
            redirect: {
                destination: signInUri,
                permanent: false,
            },
        };
    });
}
