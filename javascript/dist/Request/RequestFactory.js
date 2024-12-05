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
exports.RequestFactory = RequestFactory;
const MakeWithoutToken_1 = require("./Make/MakeWithoutToken");
const RequestMethods_1 = __importDefault(require("./RequestMethods"));
const MakeWithToken_1 = require("./Make/MakeWithToken");
const MakeWithSignInRedirect_1 = require("./Make/MakeWithSignInRedirect");
function RequestFactory({ appUrl, requestBaseUrl, tokenRepository, nextAuthProviderId, refreshAccessToken, }) {
    return {
        makeWithoutToken: (_a) => __awaiter(this, [_a], void 0, function* ({ uri = '', method = RequestMethods_1.default.GET, queryParams = new URLSearchParams(), payload = {}, cacheTags = [], cacheSeconds = 300, }) {
            return (0, MakeWithoutToken_1.MakeWithoutToken)({
                uri,
                method,
                queryParams,
                payload,
                cacheTags,
                cacheSeconds,
            }, requestBaseUrl);
        }),
        makeWithToken: (_a) => __awaiter(this, [_a], void 0, function* ({ uri = '', method = RequestMethods_1.default.GET, queryParams = new URLSearchParams(), payload = {}, cacheTags = [], cacheSeconds = 300, }) {
            return (0, MakeWithToken_1.MakeWithToken)({
                uri,
                method,
                queryParams,
                payload,
                cacheTags,
                cacheSeconds,
            }, requestBaseUrl, nextAuthProviderId, tokenRepository, refreshAccessToken);
        }),
        makeWithSignInRedirect: (_a) => __awaiter(this, [_a], void 0, function* ({ uri = '', method = RequestMethods_1.default.GET, queryParams = new URLSearchParams(), payload = {}, cacheTags = [], cacheSeconds = 300, }) {
            return (0, MakeWithSignInRedirect_1.MakeWithSignInRedirect)({
                uri,
                method,
                queryParams,
                payload,
                cacheTags,
                cacheSeconds,
            }, appUrl, requestBaseUrl, nextAuthProviderId, tokenRepository, refreshAccessToken);
        }),
    };
}
