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
exports.RefreshAccessTokenFactory = RefreshAccessTokenFactory;
const WellKnown_1 = require("../../WellKnown");
function requestRefreshedToken(token, wellKnownUrl, clientId, clientSecret) {
    return __awaiter(this, void 0, void 0, function* () {
        const wellKnown = yield (0, WellKnown_1.GetWellKnown)(wellKnownUrl);
        const { refreshToken } = token;
        return fetch(wellKnown.tokenEndpoint, {
            headers: { 'Content-Type': 'application/json' },
            method: 'POST',
            body: JSON.stringify({
                grant_type: 'refresh_token',
                refresh_token: refreshToken,
                client_id: clientId,
                client_secret: clientSecret,
            }),
        });
    });
}
function getRefreshedAccessToken(token, wellKnownUrl, clientId, clientSecret) {
    return __awaiter(this, void 0, void 0, function* () {
        try {
            const refreshResponse = yield requestRefreshedToken(token, wellKnownUrl, clientId, clientSecret);
            if (!refreshResponse.ok) {
                return null;
            }
            const refreshedJson = yield refreshResponse.json();
            return Object.assign(Object.assign({}, token), { accessToken: refreshedJson.access_token, accessTokenExpires: (new Date().getTime()) + refreshedJson.expires_in, refreshToken: refreshedJson.refresh_token });
        }
        catch (error) {
            return null;
        }
    });
}
function RefreshAccessTokenFactory({ tokenRepository, refreshLock, wellKnownUrl, clientId, clientSecret, }) {
    return () => __awaiter(this, void 0, void 0, function* () {
        const token = yield tokenRepository.getTokenFromCookies();
        // To ensure that only one request is refreshing the token we await a lock
        yield refreshLock.acquire(token.accessToken);
        /**
         * Now we check the token in the store again to make sure the token wasn't
         * already refreshed by another request
         */
        const tokenCheck = yield tokenRepository.getTokenFromCookies();
        // If the token was already refreshed while we awaited a lock
        if (tokenCheck.accessToken !== token.accessToken) {
            yield refreshLock.release(token.accessToken);
            return;
        }
        const newToken = yield getRefreshedAccessToken(token, wellKnownUrl, clientId, clientSecret);
        // If there is no token, the refresh was unsuccessful, and so we won't save
        if (!newToken) {
            yield refreshLock.release(token.accessToken);
            return;
        }
        // WE HAVE A NEW TOKEN! YAY! Now set it to the token store
        yield tokenRepository.setTokenBasedOnCookies(newToken);
        yield refreshLock.release(token.accessToken);
    });
}
