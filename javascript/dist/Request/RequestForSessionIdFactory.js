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
exports.RequestForSessionIdFactory = RequestForSessionIdFactory;
const RequestMethods_1 = __importDefault(require("./RequestMethods"));
const MakeWithTokenForSessionId_1 = require("./Make/MakeWithTokenForSessionId");
const MakeWithSignInRedirectForSessionId_1 = require("./Make/MakeWithSignInRedirectForSessionId");
function RequestForSessionIdFactory({ requestBaseUrl, tokenRepository, nextAuthProviderId, refreshAccessToken, signInUri = '/api/auth/sign-in', }) {
    return {
        makeWithToken: (sessionId_1, _a) => __awaiter(this, [sessionId_1, _a], void 0, function* (sessionId, { uri = '', method = RequestMethods_1.default.GET, queryParams = new URLSearchParams(), payload = {}, cacheTags = [], cacheSeconds = 300, headers = {}, }) {
            return (0, MakeWithTokenForSessionId_1.MakeWithTokenForSessionId)({
                uri,
                method,
                queryParams,
                payload,
                cacheTags,
                cacheSeconds,
                headers,
            }, requestBaseUrl, nextAuthProviderId, tokenRepository, refreshAccessToken, sessionId);
        }),
        makeWithSignInRedirect: (req_1, _a) => __awaiter(this, [req_1, _a], void 0, function* (req, { uri = '', method = RequestMethods_1.default.GET, queryParams = new URLSearchParams(), payload = {}, cacheTags = [], cacheSeconds = 300, headers = {}, }) {
            return (0, MakeWithSignInRedirectForSessionId_1.MakeWithSignInRedirectForSessionId)({
                uri,
                method,
                queryParams,
                payload,
                cacheTags,
                cacheSeconds,
                headers,
            }, req, requestBaseUrl, nextAuthProviderId, tokenRepository, refreshAccessToken, signInUri);
        }),
    };
}
