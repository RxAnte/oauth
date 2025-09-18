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
exports.AuthCodeGrantApiFactory = AuthCodeGrantApiFactory;
const CreateSignInRouteResponse_1 = __importDefault(require("./Internal/CreateSignInRouteResponse"));
const RespondToAuthCodeCallback_1 = __importDefault(require("./Internal/RespondToAuthCodeCallback"));
const DeleteSessionAndCookie_1 = __importDefault(require("./Internal/DeleteSessionAndCookie"));
function AuthCodeGrantApiFactory({ tokenRepository, appUrl, authorizeUrl, tokenUrl, userInfoUrl, clientId, clientSecret, callbackUri = '/api/auth/callback', audience, }) {
    return {
        createSignInRouteResponse: (request_1, ...args_1) => __awaiter(this, [request_1, ...args_1], void 0, function* (request, modifyAuthorizeUrl = () => { }) {
            return (0, CreateSignInRouteResponse_1.default)(request, appUrl, authorizeUrl, clientId, callbackUri, audience, modifyAuthorizeUrl);
        }),
        respondToAuthCodeCallback: (request_1, ...args_1) => __awaiter(this, [request_1, ...args_1], void 0, function* (request, onBeforeSuccessRedirect = () => { }) {
            return (0, RespondToAuthCodeCallback_1.default)(tokenRepository, request, appUrl, tokenUrl, userInfoUrl, clientId, clientSecret, callbackUri, onBeforeSuccessRedirect);
        }),
        deleteSessionAndCookie: () => __awaiter(this, void 0, void 0, function* () {
            return (0, DeleteSessionAndCookie_1.default)(tokenRepository);
        }),
    };
}
