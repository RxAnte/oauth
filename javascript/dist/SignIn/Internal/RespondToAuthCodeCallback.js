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
exports.default = RespondToAuthCodeCallback;
const headers_1 = require("next/headers");
const crypto_1 = require("crypto");
function RespondToAuthCodeCallback(tokenRepository_1, request_1, appUrl_1, tokenUrl_1, userInfoUrl_1, clientId_1, clientSecret_1) {
    return __awaiter(this, arguments, void 0, function* (tokenRepository, request, appUrl, tokenUrl, userInfoUrl, clientId, clientSecret, callbackUri = '/api/auth/callback') {
        var _a, _b;
        const appUrlUrl = new URL(appUrl);
        const cookieStore = yield (0, headers_1.cookies)();
        const authReturnCookie = cookieStore.get('authReturn');
        let authReturn = authReturnCookie
            ? authReturnCookie.value
            : appUrl;
        const authReturnUrl = new URL(authReturn);
        if (authReturnUrl.host !== appUrlUrl.host) {
            authReturn = appUrl;
        }
        const localState = cookieStore.get('authorizeState');
        if (!localState) {
            return new Response('Incorrect state', { status: 400 });
        }
        const { searchParams } = new URL(request.url);
        const urlState = searchParams.get('state');
        if (localState.value !== urlState) {
            return new Response('Incorrect state', { status: 400 });
        }
        const code = searchParams.get('code');
        let callbackUrl = appUrl.endsWith('/')
            ? appUrl.slice(0, -1)
            : appUrl;
        callbackUrl += callbackUri;
        const tokenResponse = yield fetch(tokenUrl, {
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            method: 'POST',
            body: JSON.stringify({
                grant_type: 'authorization_code',
                redirect_uri: callbackUrl,
                client_id: clientId,
                client_secret: clientSecret,
                code,
            }),
        });
        try {
            const responseJson = yield tokenResponse.json();
            if (tokenResponse.status !== 200) {
                const errorJson = responseJson;
                const error = (_a = errorJson.error) !== null && _a !== void 0 ? _a : 'An unknown error occurred';
                const errorDescription = (_b = errorJson.error_description) !== null && _b !== void 0 ? _b : '';
                return new Response(`${error}\n\n${errorDescription}`, { status: tokenResponse.status });
            }
            const tokenJson = responseJson;
            const userInfoRequest = yield fetch(userInfoUrl, {
                redirect: 'manual',
                method: 'GET',
                headers: {
                    Authorization: `Bearer ${tokenJson.access_token}`,
                    RequestType: 'api',
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
            });
            const userInfoJson = yield userInfoRequest.json();
            const token = {
                accessToken: tokenJson.access_token,
                accessTokenExpires: (new Date().getTime()) + tokenJson.expires_in,
                refreshToken: tokenJson.refresh_token,
                user: {
                    id: userInfoJson.sub,
                    sub: userInfoJson.sub,
                    email: userInfoJson.email,
                    name: userInfoJson.name,
                },
            };
            const sessionId = (0, crypto_1.randomBytes)(32).toString('hex');
            yield tokenRepository.setTokenFromSessionId(token, sessionId);
            cookieStore.set('oauthSessionId', sessionId, {
                httpOnly: true,
                path: '/',
                maxAge: 2628000, // One month for good measure
                secure: true,
            });
            return Response.redirect(authReturn);
        }
        catch (error) {
            return new Response('An unknown error occurred', { status: 500 });
        }
    });
}
