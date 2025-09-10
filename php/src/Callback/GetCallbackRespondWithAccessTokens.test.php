<?php

declare(strict_types=1);

use Dflydev\FigCookies\Cookie;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Callback\GetCallbackRespondWithAccessTokens;
use RxAnte\OAuth\Cookies\OauthLoginReturnCookieHandler;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\TokenRepository\TokenRepository;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('GetCallbackRespondWithAccessTokens', function (): void {
    uses()->group('GetCallbackRespondWithAccessTokens');

    it(
        'saves the token, sets the session cookie, and redirects to the login return URL',
        function (string|null $cookieValue): void {
            $outputResponse = Mockery::mock(ResponseInterface::class);
            $outputResponse->expects('withStatus')
                ->with('302')
                ->andReturnSelf();
            $outputResponse->expects('withHeader')
                ->with('Location', $cookieValue ?? '/')
                ->andReturnSelf();

            $inputResponse = Mockery::mock(ResponseInterface::class);

            $request = Mockery::mock(ServerRequestInterface::class);

            $sessionTokenCookieHandler = Mockery::mock(
                OauthSessionTokenCookieHandler::class,
            );
            $sessionTokenCookieHandler->expects('setResponseCookie')
                ->with($inputResponse, 'mock-id')
                ->andReturn($outputResponse);

            $accessToken = Mockery::mock(AccessTokenInterface::class);

            $tokenRepository = Mockery::mock(TokenRepository::class);
            $tokenRepository->expects(
                'createSessionIdWithAccessToken',
            )->with($accessToken)->andReturn('mock-id');

            $loginReturnCookie = new Cookie(
                'oauth-login-return',
                $cookieValue,
            );

            $loginReturnCookieHandler = Mockery::mock(
                OauthLoginReturnCookieHandler::class,
            );
            $loginReturnCookieHandler->expects(
                'getCookieFromRequest',
            )->with($request)->andReturn($loginReturnCookie);

            $sut = new GetCallbackRespondWithAccessTokens(
                tokenRepository: $tokenRepository,
                accessToken: $accessToken,
                loginReturnCookieHandler: $loginReturnCookieHandler,
                sessionTokenCookieHandler: $sessionTokenCookieHandler,
            );

            $response = $sut->respond(
                request: $request,
                response: $inputResponse,
            );

            expect($response)->toBe($outputResponse);
        },
    )->with([
        'with default login return' => ['cookieValue' => null],
        'with cookie login return' => ['cookieValue' => '/test/login/return'],
    ]);
});
