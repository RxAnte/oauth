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
exports.MakeWithoutToken = MakeWithoutToken;
const ParseResponse_1 = require("./ParseResponse");
function sendRequest(_a, requestBaseUrl_1) {
    return __awaiter(this, arguments, void 0, function* ({ uri, method, queryParams, payload, cacheTags, cacheSeconds, }, requestBaseUrl) {
        const url = new URL(`${requestBaseUrl}${uri}?${queryParams.toString()}`);
        const headers = new Headers({
            RequestType: 'api',
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Provider: 'auth0',
        });
        const body = JSON.stringify(payload);
        const options = {
            redirect: 'manual',
            method,
            headers,
            next: {
                tags: cacheTags,
                revalidate: cacheSeconds,
            },
        };
        if ((method !== 'HEAD' && method !== 'GET')) {
            options.body = body;
        }
        return fetch(url, options);
    });
}
function MakeWithoutToken(props, requestBaseUrl) {
    return __awaiter(this, void 0, void 0, function* () {
        return (0, ParseResponse_1.ParseResponse)(() => __awaiter(this, void 0, void 0, function* () {
            return sendRequest(props, requestBaseUrl);
        }));
    });
}
