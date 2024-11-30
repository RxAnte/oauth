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
exports.Auth0ProviderFactory = Auth0ProviderFactory;
function Auth0ProviderFactory({ wellKnownUrl, clientId, clientSecret, audience, id = 'auth0', name = 'Auth0', }) {
    return {
        wellKnown: wellKnownUrl,
        clientId,
        clientSecret,
        id,
        name,
        type: 'oauth',
        checks: ['state'],
        authorization: {
            params: {
                scope: 'openid profile email offline_access', // offline_access required for refresh tokens :/
                audience,
            },
        },
        httpOptions: {
            timeout: 30000,
        },
        userinfo: {
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-ignore
            request(_a) {
                return __awaiter(this, arguments, void 0, function* ({ client, tokens }) {
                    // Get base profile
                    // noinspection UnnecessaryLocalVariableJS
                    const profile = yield client.userinfo(
                    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                    // @ts-ignore
                    tokens);
                    return profile;
                });
            },
        },
        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
        // @ts-ignore
        profile(profile) {
            return Object.assign({ id: profile.sub }, profile);
        },
    };
}
