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
exports.CreateSessionIdWithToken = CreateSessionIdWithToken;
const crypto_1 = require("crypto");
function CreateSessionIdWithToken(token, user, redis, redisTokenExpireTimeInSeconds) {
    return __awaiter(this, void 0, void 0, function* () {
        const id = (0, crypto_1.randomUUID)();
        yield redis.set(`user_token:${id}`, JSON.stringify({
            accessToken: token.access_token,
            accessTokenExpires: token.expires_at,
            refreshToken: token.refresh_token,
            user,
        }), 'EX', redisTokenExpireTimeInSeconds);
        return id;
    });
}
