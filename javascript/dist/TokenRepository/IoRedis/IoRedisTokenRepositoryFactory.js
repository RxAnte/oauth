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
exports.IoRedisTokenRepositoryFactory = IoRedisTokenRepositoryFactory;
const CreateSessionIdWithToken_1 = require("./CreateSessionIdWithToken");
const FindTokenBySessionId_1 = require("./FindTokenBySessionId");
const FindTokenFromCookies_1 = require("./FindTokenFromCookies");
const GetTokenFromCookies_1 = require("./GetTokenFromCookies");
const SetTokenBasedOnCookies_1 = require("./SetTokenBasedOnCookies");
const SetTokenFromSessionId_1 = require("./SetTokenFromSessionId");
function IoRedisTokenRepositoryFactory({ redis, 
/** @deprecated secret is no longer require unless still using next-auth */
secret, redisTokenExpireTimeInSeconds, }) {
    return {
        /** @deprecated This was used to support next-auth and is no longer used */
        createSessionIdWithToken: (token, user) => __awaiter(this, void 0, void 0, function* () {
            return (0, CreateSessionIdWithToken_1.CreateSessionIdWithToken)(token, user, redis, redisTokenExpireTimeInSeconds);
        }),
        findTokenBySessionId: (sessionId) => __awaiter(this, void 0, void 0, function* () {
            return (0, FindTokenBySessionId_1.FindTokenBySessionId)(sessionId, redis);
        }),
        findTokenFromCookies: () => __awaiter(this, void 0, void 0, function* () {
            return (0, FindTokenFromCookies_1.FindTokenFromCookies)(redis, secret);
        }),
        getTokenFromCookies: () => __awaiter(this, void 0, void 0, function* () {
            return (0, GetTokenFromCookies_1.GetTokenFromCookies)(redis, secret);
        }),
        setTokenFromSessionId: (token, sessionId) => __awaiter(this, void 0, void 0, function* () {
            return (0, SetTokenFromSessionId_1.SetTokenFromSessionId)(token, sessionId, redis, redisTokenExpireTimeInSeconds);
        }),
        setTokenBasedOnCookies: (token) => __awaiter(this, void 0, void 0, function* () {
            return (0, SetTokenBasedOnCookies_1.SetTokenBasedOnCookies)(token, redis, redisTokenExpireTimeInSeconds, secret);
        }),
    };
}
