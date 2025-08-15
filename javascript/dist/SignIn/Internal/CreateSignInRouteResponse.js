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
exports.default = CreateSignInRouteResponse;
const headers_1 = require("next/headers");
const crypto_1 = require("crypto");
function CreateSignInRouteResponse(request_1, appUrl_1, authorizeUrl_1, clientId_1) {
    return __awaiter(this, arguments, void 0, function* (request, appUrl, authorizeUrl, clientId, callbackUri = '/api/auth/callback') {
        const { searchParams } = new URL(request.url);
        const authReturn = searchParams.get('authReturn') || appUrl;
        const cookieStore = yield (0, headers_1.cookies)();
        const authorizeState = (0, crypto_1.randomBytes)(32).toString('hex');
        cookieStore.set('authReturn', authReturn, {
            httpOnly: true,
            path: '/',
            maxAge: 60 * 10, // Ten minutes
            secure: true,
        });
        cookieStore.set('authorizeState', authorizeState, {
            httpOnly: true,
            path: '/',
            maxAge: 60 * 10, // Ten minutes
            secure: true,
        });
        let callbackUrl = appUrl.endsWith('/')
            ? appUrl.slice(0, -1)
            : appUrl;
        callbackUrl += callbackUri;
        const authorizeUri = new URL(authorizeUrl);
        authorizeUri.searchParams.append('response_type', 'code');
        authorizeUri.searchParams.append('client_id', clientId);
        authorizeUri.searchParams.append('redirect_uri', callbackUrl);
        authorizeUri.searchParams.append('scope', 'openid profile email offline_access');
        authorizeUri.searchParams.append('state', authorizeState);
        return Response.redirect(authorizeUri.toString(), 302);
    });
}
