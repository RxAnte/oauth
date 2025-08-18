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
exports.GetWellKnown = GetWellKnown;
const zod_1 = require("zod");
const MD5_1 = __importDefault(require("./MD5"));
const WellKnownSchema = zod_1.z.object({
    authorization_endpoint: zod_1.z.string(),
    token_endpoint: zod_1.z.string(),
    userinfo_endpoint: zod_1.z.string(),
});
function GetWellKnown(wellKnownUrl_1, redis_1) {
    return __awaiter(this, arguments, void 0, function* (wellKnownUrl, redis, wellKnownCacheKey = 'rxante_oauth_well_known', wellKnownCacheExpiresInSeconds = 86400) {
        const cacheKey = `${wellKnownCacheKey}_${(0, MD5_1.default)(wellKnownUrl)}`;
        if (redis) {
            const redisStore = yield redis.get(cacheKey);
            if (redisStore) {
                return JSON.parse(redisStore);
            }
        }
        const response = yield fetch(wellKnownUrl);
        const wellKnownJson = WellKnownSchema.parse(yield response.json());
        const wellKnown = {
            authorizationEndpoint: wellKnownJson.authorization_endpoint,
            tokenEndpoint: wellKnownJson.token_endpoint,
            userinfoEndpoint: wellKnownJson.userinfo_endpoint,
        };
        if (redis) {
            yield redis.set(cacheKey, JSON.stringify(wellKnown), 'EX', wellKnownCacheExpiresInSeconds);
        }
        return wellKnown;
    });
}
