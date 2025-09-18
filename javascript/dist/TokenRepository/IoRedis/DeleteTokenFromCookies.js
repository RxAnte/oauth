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
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = DeleteTokenFromCookies;
const GetIdFromCookies_1 = require("./GetIdFromCookies");
const DeleteTokenBySessionId_1 = __importDefault(require("./DeleteTokenBySessionId"));
function DeleteTokenFromCookies(redis) {
    return __awaiter(this, void 0, void 0, function* () {
        const sessionId = yield (0, GetIdFromCookies_1.GetIdFromCookies)();
        if (!sessionId) {
            return;
        }
        yield (0, DeleteTokenBySessionId_1.default)(sessionId, redis);
    });
}
