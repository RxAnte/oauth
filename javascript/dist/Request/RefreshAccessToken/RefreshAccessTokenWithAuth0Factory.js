"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.RefreshAccessTokenWithAuth0Factory = RefreshAccessTokenWithAuth0Factory;
const RefreshAccessTokenFactory_1 = require("./RefreshAccessTokenFactory");
function RefreshAccessTokenWithAuth0Factory({ tokenRepository, refreshLock, wellKnownUrl, clientId, clientSecret, }) {
    return (0, RefreshAccessTokenFactory_1.RefreshAccessTokenFactory)({
        tokenRepository,
        refreshLock,
        wellKnownUrl,
        clientId,
        clientSecret,
    });
}
