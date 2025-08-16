"use strict";
'use client';
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = SignInPage;
const react_1 = __importStar(require("react"));
const react_2 = require("next-auth/react");
const react_loading_1 = __importDefault(require("react-loading"));
/**
 * @deprecated RxAnte Oauth is moving away from next-auth. Use the AuthCodeGrantApi instead
 */
function SignInPage({ providerId, CustomLoadingPage, reactLoadingType = 'spin', reactLoadingColor = '#000', }) {
    (0, react_1.useEffect)(() => {
        var _a;
        const url = new URL(window.location.href);
        (0, react_2.signIn)(providerId, {
            callbackUrl: (_a = url.searchParams.get('authReturn')) === null || _a === void 0 ? void 0 : _a.toString(),
        });
    });
    if (CustomLoadingPage) {
        return <CustomLoadingPage />;
    }
    return (<div style={{
            position: 'fixed',
            top: 0,
            left: 0,
            bottom: 0,
            right: 0,
            width: '100%',
            height: '100vh',
            zIndex: '999',
            overflow: 'hidden',
            backgroundColor: 'white',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            justifyContent: 'center',
        }}>
            <react_loading_1.default type={reactLoadingType} color={reactLoadingColor}/>
        </div>);
}
