"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.TokenDataSchema = void 0;
const zod_1 = require("zod");
const User_1 = require("./User");
exports.TokenDataSchema = zod_1.z.object({
    accessToken: zod_1.z.string(),
    accessTokenExpires: zod_1.z.number(),
    refreshToken: zod_1.z.string(),
    user: User_1.UserSchema,
});
