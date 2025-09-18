import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import { AuthCodeGrantApiFactory } from './AuthCodeGrantApiFactory';
import CreateSignInRouteResponse from './Internal/CreateSignInRouteResponse';
import RespondToAuthCodeCallback from './Internal/RespondToAuthCodeCallback';
import DeleteSessionAndCookie from './Internal/DeleteSessionAndCookie';
import { TokenRepository } from '../TokenRepository/TokenRepository';

vi.mock('./Internal/CreateSignInRouteResponse', () => ({
    default: vi.fn(),
}));

vi.mock('./Internal/RespondToAuthCodeCallback', () => ({
    default: vi.fn(),
}));

vi.mock('./Internal/DeleteSessionAndCookie', () => ({
    default: vi.fn(),
}));

describe('AuthCodeGrantApiFactory', () => {
    let mockTokenRepository: TokenRepository;

    beforeEach(() => {
        vi.resetAllMocks();

        mockTokenRepository = {} as TokenRepository;

        const signInRouteResponse = new Response('signInRouteResponse');

        const authCodeCallbackResponse = new Response(
            'authCodeCallbackResponse',
        );

        vi.mocked(CreateSignInRouteResponse).mockReturnValue(Promise.resolve(
            signInRouteResponse,
        ));

        vi.mocked(RespondToAuthCodeCallback).mockReturnValue(Promise.resolve(
            authCodeCallbackResponse,
        ));
    });

    it('creates an AuthCodeGrantApi', async () => {
        const api = AuthCodeGrantApiFactory({
            tokenRepository: mockTokenRepository,
            appUrl: 'https://localhost',
            authorizeUrl: 'https://auth.com/authorize',
            tokenUrl: 'https://auth.com/token',
            userInfoUrl: 'https://auth.com/userinfo',
            clientId: 'mock-client-id',
            clientSecret: 'mock-client-secret',
            callbackUri: '/mock/callback/uri',
            audience: 'mock-audience',
        });

        /**
         * createSignInRouteResponse
         */

        const signInMockRequest = new Request('https://signin');

        const modifyAuthUrl = () => {};

        const signInResponse = await api.createSignInRouteResponse(
            signInMockRequest,
            modifyAuthUrl,
        );

        expect(CreateSignInRouteResponse).toHaveBeenCalledWith(
            signInMockRequest,
            'https://localhost',
            'https://auth.com/authorize',
            'mock-client-id',
            '/mock/callback/uri',
            'mock-audience',
            modifyAuthUrl,
        );

        expect(await signInResponse.text()).toBe(
            'signInRouteResponse',
        );

        /**
         * respondToAuthCodeCallback
         */

        const authCodeMockRequest = new Request('https://authCode');

        const authCodeResponse = await api.respondToAuthCodeCallback(
            authCodeMockRequest,
        );

        expect(RespondToAuthCodeCallback).toHaveBeenCalledWith(
            mockTokenRepository,
            authCodeMockRequest,
            'https://localhost',
            'https://auth.com/token',
            'https://auth.com/userinfo',
            'mock-client-id',
            'mock-client-secret',
            '/mock/callback/uri',
            expect.any(Function),
        );

        expect(await authCodeResponse.text()).toBe(
            'authCodeCallbackResponse',
        );

        /**
         * deleteSessionAndCookie
         */
        await api.deleteSessionAndCookie();

        expect(DeleteSessionAndCookie).toHaveBeenCalledWith(mockTokenRepository);
    });

    it(
        'creates an AuthCodeGrantApi using defaults with createSignInRouteResponse and respondToAuthCodeCallback methods',
        async () => {
            const api = AuthCodeGrantApiFactory({
                tokenRepository: mockTokenRepository,
                appUrl: 'https://localhostmock',
                authorizeUrl: 'https://auth.com/authorize/mock',
                tokenUrl: 'https://auth.com/token/mock',
                userInfoUrl: 'https://auth.com/userinfo/mock',
                clientId: 'client-id',
                clientSecret: 'client-secret',
            });

            const signInMockRequest = new Request('https://signin');

            const signInResponse = await api.createSignInRouteResponse(
                signInMockRequest,
            );

            expect(CreateSignInRouteResponse).toHaveBeenCalledWith(
                signInMockRequest,
                'https://localhostmock',
                'https://auth.com/authorize/mock',
                'client-id',
                '/api/auth/callback',
                undefined,
                expect.any(Function),
            );

            expect(await signInResponse.text()).toBe(
                'signInRouteResponse',
            );

            const authCodeMockRequest = new Request('https://authCode');

            const onBeforeSuccessRedirect = () => {};

            const authCodeResponse = await api.respondToAuthCodeCallback(
                authCodeMockRequest,
                onBeforeSuccessRedirect,
            );

            expect(RespondToAuthCodeCallback).toHaveBeenCalledWith(
                mockTokenRepository,
                authCodeMockRequest,
                'https://localhostmock',
                'https://auth.com/token/mock',
                'https://auth.com/userinfo/mock',
                'client-id',
                'client-secret',
                '/api/auth/callback',
                onBeforeSuccessRedirect,
            );

            expect(await authCodeResponse.text()).toBe(
                'authCodeCallbackResponse',
            );
        },
    );
});
