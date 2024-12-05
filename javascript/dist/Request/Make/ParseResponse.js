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
exports.ParseResponse = ParseResponse;
const AccessDeniedResponse_1 = require("./AccessDeniedResponse");
const RequestAuthenticationError_1 = __importDefault(require("../RequestAuthenticationError"));
function ParseResponse(runRequest) {
    return __awaiter(this, void 0, void 0, function* () {
        try {
            const response = yield runRequest();
            const responseBody = response.body;
            /**
             * `.text()` can only be used once, second time will throw exception, so
             * we set it to a variable to be used in more places
             */
            const responseText = yield response.text();
            /**
             * We're going to try decoding the API JSON response. But we want to
             * catch any errors like if the API didn't return JSON
             */
            let json = {};
            try {
                /**
                 * Auth0 returns a string of 'Unauthorized', rather than json,
                 * which causes .json() to fail
                 */
                if (response.status === 401
                    || responseText.toLowerCase() === 'unauthorized') {
                    return AccessDeniedResponse_1.AccessDeniedUserNotLoggedInResponse;
                }
                /**
                 * We can't use apiRes.json() because we used .text() above, and
                 * they're mutually exclusive and running .json() now throws
                 * an exception
                 */
                json = JSON.parse(responseText);
            }
            catch (innerError) {
                /**
                 * If the response code is not a 2xx response, we can pass the
                 * response code through. If it is a 2xx response, we don't want to
                 * pass that through since the response is not json and was not
                 * actually successful
                 */
                const status = response.ok ? 503 : response.status;
                const msg = 'The request returned an invalid response';
                return {
                    headers: response.headers,
                    ok: false,
                    status,
                    body: responseBody,
                    json: {
                        error: 'invalid_response',
                        error_description: msg,
                        message: msg,
                    },
                };
            }
            return {
                headers: response.headers,
                ok: response.ok,
                status: response.status,
                body: responseBody,
                json,
            };
        }
        catch (outerError) {
            /**
             * If this is an authentication error, we can send access denied
             */
            if (outerError instanceof RequestAuthenticationError_1.default) {
                return AccessDeniedResponse_1.AccessDeniedUserNotLoggedInResponse;
            }
            return {
                headers: new Headers(),
                ok: false,
                status: 500,
                body: null,
                json: {
                    error: 'unknown_error',
                    error_description: 'An unknown error occurred',
                    message: 'An unknown error occurred',
                },
            };
        }
    });
}
