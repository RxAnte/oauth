<?php

declare(strict_types=1);

use Dflydev\FigCookies\Cookie;
use GuzzleHttp\Client;
use Hyperf\Guzzle\ClientFactory;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\Request\Payload;
use RxAnte\OAuth\Request\QueryParams;
use RxAnte\OAuth\Request\RequestApi;
use RxAnte\OAuth\Request\RequestApiConfig;
use RxAnte\OAuth\Request\RequestMethod;
use RxAnte\OAuth\Request\RequestProperties;
use RxAnte\OAuth\TokenRepository\EmptyAccessToken;
use RxAnte\OAuth\TokenRepository\TokenRepository;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

describe('RequestApi', function (): void {
    uses()->group('RequestApi');

    class RequestApiGuzzleMockStack
    {
        public RequestInterface $sentRequest;

        public readonly ClientFactory $clientFactory;

        public function __construct()
        {
            $this->sentRequest = Mockery::mock(RequestInterface::class);

            $response = Mockery::mock(ResponseInterface::class);
            $response->allows('getReasonPhrase')
                ->andReturn('mock-reason-phrase');

            $client = Mockery::mock(Client::class);
            $client->expects('sendRequest')->andReturnUsing(
                function (RequestInterface $request) use (
                    $response,
                ): ResponseInterface {
                    $this->sentRequest = $request;

                    return $response;
                },
            );

            $clientFactory = Mockery::mock(ClientFactory::class);
            $clientFactory->expects('create')->andReturn(
                $client,
            );

            $this->clientFactory = $clientFactory;
        }
    }

    test(
        'makeWithoutToken() makes a formatted request with Guzzle client',
        function (): void {
            $guzzleMockStack = new RequestApiGuzzleMockStack();

            $sut = new RequestApi(
                config: new RequestApiConfig(),
                provider: Mockery::mock(AbstractProvider::class),
                clientFactory: $guzzleMockStack->clientFactory,
                tokenRepository: Mockery::mock(TokenRepository::class),
                sessionTokenCookieHandler: Mockery::mock(
                    OauthSessionTokenCookieHandler::class,
                ),
            );

            $requestResponse = $sut->makeWithoutToken(
                properties: new RequestProperties(
                    uri: '/foo/bar',
                    queryParams: new QueryParams(['search' => 'asdf']),
                    payload: new Payload(['mock' => 'payload']),
                ),
            );

            expect($guzzleMockStack->sentRequest->getMethod())->toBe(
                'GET',
            );

            expect((string) $guzzleMockStack->sentRequest->getUri())
                ->toBe(
                    '/foo/bar?search=asdf',
                );

            expect($guzzleMockStack->sentRequest->getHeaders())
                ->toHaveCount(2);

            expect(
                $guzzleMockStack->sentRequest->getHeaderLine('accept'),
            )->toBe('application/json');

            expect(
                $guzzleMockStack->sentRequest->getHeaderLine('content-type'),
            )->toBe('application/json');

            expect(
                (string) $guzzleMockStack->sentRequest->getBody(),
            )->toBe('{"mock":"payload"}');

            expect($requestResponse->getReasonPhrase())->toBe(
                'mock-reason-phrase',
            );
        },
    );

    class RequestApiProviderMockStack
    {
        public readonly RequestInterface $getAuthRequestReturn;

        public readonly AbstractProvider $provider;

        public string $getAuthReqMethod = '';

        public string $getAuthReqUrl = '';

        public AccessTokenInterface|null $getAuthReqToken = null;

        /** @phpstan-ignore-next-line */
        public array $getAuthReqOptions = [];

        public function __construct()
        {
            $this->getAuthRequestReturn = Mockery::mock(
                RequestInterface::class,
            );

            $provider = Mockery::mock(AbstractProvider::class);

            $provider->expects('getAuthenticatedRequest')
                ->andReturnUsing(
                    function (
                        string $method,
                        string $url,
                        AccessTokenInterface $token,
                        array $options = [],
                    ): RequestInterface {
                        $this->getAuthReqMethod = $method;

                        $this->getAuthReqUrl = $url;

                        $this->getAuthReqToken = $token;

                        $this->getAuthReqOptions = $options;

                        return $this->getAuthRequestReturn;
                    },
                );

            $this->provider = $provider;
        }
    }

    test(
        'makeWithToken() makes a formatted request with Guzzle client',
        function (): void {
            $providerMockStack = new RequestApiProviderMockStack();

            $guzzleMockStack = new RequestApiGuzzleMockStack();

            $token = new EmptyAccessToken();

            $sut = new RequestApi(
                config: new RequestApiConfig(
                    requestBaseUrl: 'https://mock-base-url/',
                ),
                provider: $providerMockStack->provider,
                clientFactory: $guzzleMockStack->clientFactory,
                tokenRepository: Mockery::mock(TokenRepository::class),
                sessionTokenCookieHandler: Mockery::mock(
                    OauthSessionTokenCookieHandler::class,
                ),
            );

            $requestResponse = $sut->makeWithToken(
                token: $token,
                properties: new RequestProperties(
                    method: RequestMethod::POST,
                    uri: '/mock/uri',
                    payload: new Payload(['foo' => 'bar']),
                ),
            );

            expect($providerMockStack->getAuthRequestReturn)->toBe(
                $guzzleMockStack->sentRequest,
            );

            expect($providerMockStack->getAuthReqMethod)->toBe(
                'POST',
            );

            expect($providerMockStack->getAuthReqUrl)->toBe(
                'https://mock-base-url/mock/uri',
            );

            expect($providerMockStack->getAuthReqToken)->toBe(
                $token,
            );

            expect($providerMockStack->getAuthReqOptions)->toBe(
                [
                    'headers' => [
                        'Accept' => ['application/json'],
                        'Content-Type' => ['application/json'],
                    ],
                    'body' => '{"foo":"bar"}',
                ],
            );

            expect($requestResponse->getReasonPhrase())->toBe(
                'mock-reason-phrase',
            );
        },
    );

    test(
        'makeWithTokenFromRequestCookies() makes a formatted request with Guzzle client when no cookie is present',
        function (): void {
            $providerMockStack = new RequestApiProviderMockStack();

            $guzzleMockStack = new RequestApiGuzzleMockStack();

            $token = new EmptyAccessToken();

            $serverRequest = Mockery::mock(
                ServerRequestInterface::class,
            );

            $sessionTokenCookieHandler = Mockery::mock(
                OauthSessionTokenCookieHandler::class,
            );
            $sessionTokenCookieHandler->expects(
                'getCookieFromRequest',
            )
                ->with($serverRequest)
                ->andReturn(new Cookie(
                    'mock-empty-cookie',
                    null,
                ));

            $tokenRepository = Mockery::mock(TokenRepository::class);
            $tokenRepository->expects('getTokenBySessionId')
                ->with('noop')
                ->andReturn($token);

            $sut = new RequestApi(
                config: new RequestApiConfig(
                    requestBaseUrl: 'https://mock-base-url/',
                ),
                provider: $providerMockStack->provider,
                clientFactory: $guzzleMockStack->clientFactory,
                tokenRepository: $tokenRepository,
                sessionTokenCookieHandler: $sessionTokenCookieHandler,
            );

            $requestResponse = $sut->makeWithTokenFromRequestCookies(
                serverRequest: $serverRequest,
                properties: new RequestProperties(
                    method: RequestMethod::POST,
                    uri: '/mock/uri',
                    payload: new Payload(['foo' => 'bar']),
                ),
            );

            expect($providerMockStack->getAuthRequestReturn)->toBe(
                $guzzleMockStack->sentRequest,
            );

            expect($providerMockStack->getAuthReqMethod)->toBe(
                'POST',
            );

            expect($providerMockStack->getAuthReqUrl)->toBe(
                'https://mock-base-url/mock/uri',
            );

            expect($providerMockStack->getAuthReqToken)->toBe(
                $token,
            );

            expect($providerMockStack->getAuthReqOptions)->toBe(
                [
                    'headers' => [
                        'Accept' => ['application/json'],
                        'Content-Type' => ['application/json'],
                    ],
                    'body' => '{"foo":"bar"}',
                ],
            );

            expect($requestResponse->getReasonPhrase())->toBe(
                'mock-reason-phrase',
            );
        },
    );

    test(
        'makeWithTokenFromRequestCookies() makes a formatted request with Guzzle client when cookie is present',
        function (): void {
            $providerMockStack = new RequestApiProviderMockStack();

            $guzzleMockStack = new RequestApiGuzzleMockStack();

            $token = new EmptyAccessToken();

            $serverRequest = Mockery::mock(
                ServerRequestInterface::class,
            );

            $sessionTokenCookieHandler = Mockery::mock(
                OauthSessionTokenCookieHandler::class,
            );
            $sessionTokenCookieHandler->expects(
                'getCookieFromRequest',
            )
                ->with($serverRequest)
                ->andReturn(new Cookie(
                    'mock-cookie',
                    'foo-id',
                ));

            $tokenRepository = Mockery::mock(TokenRepository::class);
            $tokenRepository->expects('getTokenBySessionId')
                ->with('foo-id')
                ->andReturn($token);

            $sut = new RequestApi(
                config: new RequestApiConfig(
                    requestBaseUrl: 'https://mock-base-url/',
                ),
                provider: $providerMockStack->provider,
                clientFactory: $guzzleMockStack->clientFactory,
                tokenRepository: $tokenRepository,
                sessionTokenCookieHandler: $sessionTokenCookieHandler,
            );

            $requestResponse = $sut->makeWithTokenFromRequestCookies(
                serverRequest: $serverRequest,
                properties: new RequestProperties(
                    method: RequestMethod::POST,
                    uri: '/mock/uri',
                    payload: new Payload(['foo' => 'bar']),
                ),
            );

            expect($providerMockStack->getAuthRequestReturn)->toBe(
                $guzzleMockStack->sentRequest,
            );

            expect($providerMockStack->getAuthReqMethod)->toBe(
                'POST',
            );

            expect($providerMockStack->getAuthReqUrl)->toBe(
                'https://mock-base-url/mock/uri',
            );

            expect($providerMockStack->getAuthReqToken)->toBe(
                $token,
            );

            expect($providerMockStack->getAuthReqOptions)->toBe(
                [
                    'headers' => [
                        'Accept' => ['application/json'],
                        'Content-Type' => ['application/json'],
                    ],
                    'body' => '{"foo":"bar"}',
                ],
            );

            expect($requestResponse->getReasonPhrase())->toBe(
                'mock-reason-phrase',
            );
        },
    );

    test(
        'makeWithTokenFromCookieGlobals() makes a formatted request with Guzzle client when no cookie is present',
        function (): void {
            $providerMockStack = new RequestApiProviderMockStack();

            $guzzleMockStack = new RequestApiGuzzleMockStack();

            $token = new EmptyAccessToken();

            $tokenRepository = Mockery::mock(TokenRepository::class);
            $tokenRepository->expects('getTokenBySessionId')
                ->with('noop')
                ->andReturn($token);

            $sessionTokenCookieHandler = Mockery::mock(
                OauthSessionTokenCookieHandler::class,
            );
            $sessionTokenCookieHandler->expects('getCookieName')
                ->andReturn('mock-cookie-name');

            $sut = new RequestApi(
                config: new RequestApiConfig(
                    requestBaseUrl: 'https://mock-base-url/',
                ),
                provider: $providerMockStack->provider,
                clientFactory: $guzzleMockStack->clientFactory,
                tokenRepository: $tokenRepository,
                sessionTokenCookieHandler: $sessionTokenCookieHandler,
            );

            $requestResponse = $sut->makeWithTokenFromCookieGlobals(
                properties: new RequestProperties(
                    method: RequestMethod::POST,
                    uri: '/mock/uri',
                    payload: new Payload(['foo' => 'bar']),
                ),
            );

            expect($providerMockStack->getAuthRequestReturn)->toBe(
                $guzzleMockStack->sentRequest,
            );

            expect($providerMockStack->getAuthReqMethod)->toBe(
                'POST',
            );

            expect($providerMockStack->getAuthReqUrl)->toBe(
                'https://mock-base-url/mock/uri',
            );

            expect($providerMockStack->getAuthReqToken)->toBe(
                $token,
            );

            expect($providerMockStack->getAuthReqOptions)->toBe(
                [
                    'headers' => [
                        'Accept' => ['application/json'],
                        'Content-Type' => ['application/json'],
                    ],
                    'body' => '{"foo":"bar"}',
                ],
            );

            expect($requestResponse->getReasonPhrase())->toBe(
                'mock-reason-phrase',
            );
        },
    );

    test(
        'makeWithTokenFromCookieGlobals() makes a formatted request with Guzzle client when cookie is present',
        function (): void {
            $providerMockStack = new RequestApiProviderMockStack();

            $guzzleMockStack = new RequestApiGuzzleMockStack();

            $token = new EmptyAccessToken();

            $tokenRepository = Mockery::mock(TokenRepository::class);
            $tokenRepository->expects('getTokenBySessionId')
                ->with('mock-cookie-value')
                ->andReturn($token);

            $sessionTokenCookieHandler = Mockery::mock(
                OauthSessionTokenCookieHandler::class,
            );
            $sessionTokenCookieHandler->expects('getCookieName')
                ->andReturn('mock-cookie-name');

            $sut = new RequestApi(
                config: new RequestApiConfig(
                    requestBaseUrl: 'https://mock-base-url/',
                ),
                provider: $providerMockStack->provider,
                clientFactory: $guzzleMockStack->clientFactory,
                tokenRepository: $tokenRepository,
                sessionTokenCookieHandler: $sessionTokenCookieHandler,
                cookieGlobals: ['mock-cookie-name' => 'mock-cookie-value'],
            );

            $requestResponse = $sut->makeWithTokenFromCookieGlobals(
                properties: new RequestProperties(
                    method: RequestMethod::POST,
                    uri: 'https://foo-bar.baz/mock/uri',
                    payload: new Payload(['foo' => 'bar']),
                ),
            );

            expect($providerMockStack->getAuthRequestReturn)->toBe(
                $guzzleMockStack->sentRequest,
            );

            expect($providerMockStack->getAuthReqMethod)->toBe(
                'POST',
            );

            expect($providerMockStack->getAuthReqUrl)->toBe(
                'https://foo-bar.baz/mock/uri',
            );

            expect($providerMockStack->getAuthReqToken)->toBe(
                $token,
            );

            expect($providerMockStack->getAuthReqOptions)->toBe(
                [
                    'headers' => [
                        'Accept' => ['application/json'],
                        'Content-Type' => ['application/json'],
                    ],
                    'body' => '{"foo":"bar"}',
                ],
            );

            expect($requestResponse->getReasonPhrase())->toBe(
                'mock-reason-phrase',
            );
        },
    );
});
