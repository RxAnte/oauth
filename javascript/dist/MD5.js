"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = MD5;
const node_crypto_1 = require("node:crypto");
function MD5(content) {
    return (0, node_crypto_1.createHash)('md5').update(content).digest('hex');
}
