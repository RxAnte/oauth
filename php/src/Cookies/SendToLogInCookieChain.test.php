<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use RxAnte\OAuth\Cookies\OauthLoginReturnCookieHandler;
use RxAnte\OAuth\Cookies\OauthPkceCodeCookieHandler;
use RxAnte\OAuth\Cookies\OauthStateCookieHandler;
use RxAnte\OAuth\Cookies\SendToLogInCookieChain;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('SendToLogInCookieChain', function (): void {
    uses()->group('SendToLogInCookieChain');

    it(
        'sets all cookies on the response',
        function (
            string $path,
            string $query,
            string $expectedLoginReturn,
        ): void {
            $response1     = Mockery::mock(ResponseInterface::class);
            $response2     = Mockery::mock(ResponseInterface::class);
            $response3     = Mockery::mock(ResponseInterface::class);
            $finalResponse = Mockery::mock(ResponseInterface::class);

            $loginReturnCookieHandler = Mockery::mock(
                OauthLoginReturnCookieHandler::class,
            );
            $loginReturnCookieHandler->expects('setResponseCookie')
                ->with($response1, $expectedLoginReturn)
                ->andReturn($response2);

            $pkceCodeCookieHandler = Mockery::mock(
                OauthPkceCodeCookieHandler::class,
            );
            $pkceCodeCookieHandler->expects('setResponseCookie')
                ->with($response2, 'mock-pkce-code')
                ->andReturn($response3);

            $stateCookieHandler = Mockery::mock(
                OauthStateCookieHandler::class,
            );
            $stateCookieHandler->expects('setResponseCookie')
                ->with($response3, 'mock-oauth-state')
                ->andReturn($finalResponse);

            $uri = Mockery::mock(UriInterface::class);
            $uri->allows('getPath')->andReturn($path);
            $uri->allows('getQuery')->andReturn($query);

            $request = Mockery::mock(ServerRequestInterface::class);
            $request->allows('getUri')->andReturn($uri);

            $sut = new SendToLogInCookieChain(
                loginReturnCookieHandler: $loginReturnCookieHandler,
                pkceCodeCookieHandler: $pkceCodeCookieHandler,
                stateCookieHandler: $stateCookieHandler,
            );

            $result = $sut->set(
                request: $request,
                response: $response1,
                oauthPkceCode: 'mock-pkce-code',
                oauthState: 'mock-oauth-state',
            );

            expect($result)->toBe($finalResponse);
        },
    )->with([
        'with query string' => [
            'path' => '/foo/bar',
            'query' => 'baz=qux',
            'expectedLoginReturn' => '/foo/bar?baz=qux',
        ],
        'without query string' => [
            'path' => '/foo/bar',
            'query' => '',
            'expectedLoginReturn' => '/foo/bar',
        ],
    ]);
});
