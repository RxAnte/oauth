"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.OAUTH_SESSION_ID_COOKIE = void 0;
exports.SessionIdFromPagesRequest = SessionIdFromPagesRequest;
exports.OAUTH_SESSION_ID_COOKIE = 'oauthSessionId';
function SessionIdFromPagesRequest({ cookies }) {
    var _a;
    return (_a = cookies[exports.OAUTH_SESSION_ID_COOKIE]) !== null && _a !== void 0 ? _a : null;
}
