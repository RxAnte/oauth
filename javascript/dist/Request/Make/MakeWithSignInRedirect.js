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
exports.MakeWithSignInRedirect = MakeWithSignInRedirect;
const navigation_1 = require("next/navigation");
const headers_1 = require("next/headers");
const MakeWithToken_1 = require("./MakeWithToken");
function MakeWithSignInRedirect(props_1, appUrl_1, requestBaseUrl_1, nextAuthProviderId_1, tokenRepository_1, refreshAccessToken_1) {
    return __awaiter(this, arguments, void 0, function* (props, appUrl, requestBaseUrl, 
    /** @deprecated RxAnte Oauth is moving away from next-auth. */
    nextAuthProviderId, tokenRepository, refreshAccessToken, signInUri = '/api/auth/sign-in') {
        const response = yield (0, MakeWithToken_1.MakeWithToken)(props, requestBaseUrl, nextAuthProviderId, tokenRepository, refreshAccessToken);
        if (response.status !== 401) {
            return response;
        }
        const headersCollection = yield (0, headers_1.headers)();
        const uri = headersCollection.get('middleware-pathname') || '/';
        let authReturn = appUrl + uri;
        const authReturnQueryString = headersCollection.get('middleware-search-params') || '';
        if (authReturnQueryString) {
            authReturn += `?${authReturnQueryString}`;
        }
        const queryString = new URLSearchParams({
            authReturn: encodeURI(authReturn),
        });
        (0, navigation_1.redirect)(`${appUrl}${signInUri}?${queryString.toString()}`);
    });
}
