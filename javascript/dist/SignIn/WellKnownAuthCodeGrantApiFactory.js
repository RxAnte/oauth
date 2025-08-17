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
exports.WellKnownAuthCodeGrantApiFactory = WellKnownAuthCodeGrantApiFactory;
const WellKnown_1 = require("../WellKnown");
const AuthCodeGrantApiFactory_1 = require("./AuthCodeGrantApiFactory");
function WellKnownAuthCodeGrantApiFactory(_a) {
    return __awaiter(this, arguments, void 0, function* ({ tokenRepository, appUrl, wellKnownUrl, clientId, clientSecret, callbackUri = '/api/auth/callback', redis, wellKnownCacheKey = 'rxante_oauth_well_known', wellKnownCacheExpiresInSeconds = 86400, // cache for 1 day by default
     }) {
        const wellKnown = yield (0, WellKnown_1.GetWellKnown)(wellKnownUrl, redis, wellKnownCacheKey, wellKnownCacheExpiresInSeconds);
        return (0, AuthCodeGrantApiFactory_1.AuthCodeGrantApiFactory)({
            tokenRepository,
            appUrl,
            authorizeUrl: wellKnown.authorizationEndpoint,
            tokenUrl: wellKnown.tokenEndpoint,
            userInfoUrl: wellKnown.userinfoEndpoint,
            clientId,
            clientSecret,
            callbackUri,
        });
    });
}
