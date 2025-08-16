"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.TokenDataSchemaSchema = void 0;
const zod_1 = require("zod");
const User_1 = require("./User");
exports.TokenDataSchemaSchema = zod_1.z.object({
    accessToken: zod_1.z.string(),
    accessTokenExpires: zod_1.z.number(),
    refreshToken: zod_1.z.string(),
    user: User_1.UserSchema,
});
