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
exports.FindTokenFromCookies = FindTokenFromCookies;
const FindTokenBySessionId_1 = require("./FindTokenBySessionId");
const GetIdFromCookies_1 = require("./GetIdFromCookies");
function FindTokenFromCookies(redis, secret) {
    return __awaiter(this, void 0, void 0, function* () {
        const sessionId = yield (0, GetIdFromCookies_1.GetIdFromCookies)(secret);
        if (!sessionId) {
            return null;
        }
        return (0, FindTokenBySessionId_1.FindTokenBySessionId)(sessionId, redis);
    });
}
