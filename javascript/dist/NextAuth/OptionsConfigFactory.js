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
exports.OptionsConfigFactory = OptionsConfigFactory;
/**
 * @deprecated RxAnte Oauth is moving away from next-auth. Use the AuthCodeGrantApi instead
 */
function OptionsConfigFactory({ secret, providers, tokenRepository, debug = false, }) {
    return {
        secret,
        providers,
        debug,
        callbacks: {
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-ignore
            jwt: (_a) => __awaiter(this, [_a], void 0, function* ({ token, user, account, }) {
                // Initial sign in
                if (account && user) {
                    const sessionId = yield tokenRepository.createSessionIdWithToken(account, user);
                    return {
                        sessionId,
                        user,
                    };
                }
                return token;
            }),
            session: (_a) => __awaiter(this, [_a], void 0, function* ({ session, token }) {
                if (token.user) {
                    session.user = token.user;
                }
                // @ts-expect-error TS2339
                session.accessToken = token.accessToken;
                // @ts-expect-error TS2339
                session.error = token.error;
                return session;
            }),
        },
    };
}
