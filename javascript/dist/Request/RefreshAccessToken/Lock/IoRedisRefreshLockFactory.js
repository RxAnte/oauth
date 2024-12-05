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
exports.IoRedisRefreshLockFactory = IoRedisRefreshLockFactory;
const sleep_promise_1 = __importDefault(require("sleep-promise"));
function IoRedisRefreshLockFactory({ redis, }) {
    return {
        acquire: (accessToken) => __awaiter(this, void 0, void 0, function* () {
            let resp = null;
            let tries = 0;
            let acquiredLock = false;
            do {
                // eslint-disable-next-line no-await-in-loop
                resp = yield redis.set(`refresh_token_lock:${accessToken}`, 'true', 'EX', 60, 'NX');
                if (resp !== null && resp.toLowerCase() === 'ok') {
                    acquiredLock = true;
                    break;
                }
                tries += 1;
                // eslint-disable-next-line no-await-in-loop
                yield (0, sleep_promise_1.default)(1000);
            } while (tries < 65);
            if (acquiredLock) {
                return;
            }
            throw new Error('Could not acquire lock');
        }),
        release: (accessToken) => __awaiter(this, void 0, void 0, function* () {
            yield redis.del(`refresh_token_lock:${accessToken}`);
        }),
    };
}
