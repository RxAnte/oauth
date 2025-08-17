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
exports.GetWellKnown = GetWellKnown;
const zod_1 = require("zod");
const WellKnownSchema = zod_1.z.object({
    authorization_endpoint: zod_1.z.string(),
    token_endpoint: zod_1.z.string(),
    userinfo_endpoint: zod_1.z.string(),
});
function GetWellKnown(wellKnownUrl) {
    return __awaiter(this, void 0, void 0, function* () {
        const response = yield fetch(wellKnownUrl, {
            cache: 'force-cache',
            // @ts-expect-error TS2769
            cacheSeconds: 86400, // cache for 1 day
        });
        const wellKnownJson = WellKnownSchema.parse(yield response.json());
        return {
            authorizationEndpoint: wellKnownJson.authorization_endpoint,
            tokenEndpoint: wellKnownJson.token_endpoint,
            userinfoEndpoint: wellKnownJson.userinfo_endpoint,
        };
    });
}
