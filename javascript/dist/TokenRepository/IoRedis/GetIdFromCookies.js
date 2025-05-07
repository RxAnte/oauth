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
exports.GetIdFromCookies = GetIdFromCookies;
const headers_1 = require("next/headers");
const jwt_1 = require("next-auth/jwt");
function GetIdFromCookies(secret) {
    return __awaiter(this, void 0, void 0, function* () {
        // In Next15, `cookies()` must be awaited
        const cookieStore = yield (0, headers_1.cookies)();
        let cookie = '';
        const sessionTokenCookies = cookieStore.getAll().filter((cookieObj) => cookieObj.name.startsWith('__Secure-next-auth.session-token'));
        sessionTokenCookies.forEach((cookieObj) => {
            cookie += cookieObj.value;
        });
        if (!cookie) {
            return null;
        }
        const cookieDecoded = yield (0, jwt_1.decode)({
            token: cookie,
            secret,
        });
        return cookieDecoded === null || cookieDecoded === void 0 ? void 0 : cookieDecoded.sessionId;
    });
}
