<?php

declare(strict_types=1);

use Dflydev\FigCookies\Cookie;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Mockery\MockInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Callback\GetCallbackResponder;
use RxAnte\OAuth\Callback\GetCallbackResponderFactory;
use RxAnte\OAuth\Callback\GetCallbackRespondWithAccessTokens;
use RxAnte\OAuth\Callback\GetCallbackRespondWithInvalidState;
use RxAnte\OAuth\Callback\QueryParams;
use RxAnte\OAuth\Cookies\OauthLoginReturnCookieHandler;
use RxAnte\OAuth\Cookies\OauthPkceCodeCookieHandler;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\Cookies\OauthStateCookieHandler;
use RxAnte\OAuth\TokenRepository\TokenRepository;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('GetCallbackResponderFactory', function (): void {
    uses()->group('GetCallbackResponderFactory');

    readonly class GetCallbackResponderFactoryTestSetup
    {
        public GetCallbackResponderFactory $sut;
        public MockInterface&AbstractProvider $provider;
        public MockInterface&TokenRepository $tokenRepository;
        public MockInterface&OauthStateCookieHandler $stateCookieHandler;
        public MockInterface&OauthPkceCodeCookieHandler $pkceCodeCookieHandler;
        public MockInterface&OauthLoginReturnCookieHandler $loginReturnCookieHandler;
        public MockInterface&OauthSessionTokenCookieHandler $sessionTokenCookieHandler;
        public MockInterface&GetCallbackRespondWithInvalidState $respondWithInvalidState;
        public MockInterface&GetCallbackResponder $customRespondWithInvalidState;

        public function __construct(
            bool $withCustomInvalidStateResponder = false,
        ) {
            $this->provider = Mockery::mock(AbstractProvider::class);

            $this->tokenRepository = Mockery::mock(
                TokenRepository::class,
            );

            $this->stateCookieHandler = Mockery::mock(
                OauthStateCookieHandler::class,
            );

            $this->pkceCodeCookieHandler = Mockery::mock(
                OauthPkceCodeCookieHandler::class,
            );

            $this->loginReturnCookieHandler = Mockery::mock(
                OauthLoginReturnCookieHandler::class,
            );

            $this->sessionTokenCookieHandler = Mockery::mock(
                OauthSessionTokenCookieHandler::class,
            );

            $this->respondWithInvalidState = Mockery::mock(
                GetCallbackRespondWithInvalidState::class,
            );

            $this->customRespondWithInvalidState = Mockery::mock(
                GetCallbackResponder::class,
            );

            $this->sut = new GetCallbackResponderFactory(
                provider: $this->provider,
                tokenRepository: $this->tokenRepository,
                stateCookieHandler: $this->stateCookieHandler,
                pkceCodeCookieHandler: $this->pkceCodeCookieHandler,
                loginReturnCookieHandler: $this->loginReturnCookieHandler,
                sessionTokenCookieHandler: $this->sessionTokenCookieHandler,
                respondWithInvalidState: $this->respondWithInvalidState,
                customRespondWithInvalidState: $withCustomInvalidStateResponder ?
                    $this->customRespondWithInvalidState :
                    null,
            );
        }
    }

    it(
        'returns a responder with access tokens on success',
        function (): void {
            $setup = new GetCallbackResponderFactoryTestSetup();

            $request = Mockery::mock(ServerRequestInterface::class);

            $params = new QueryParams('mock-code', 'mock-state');

            $accessToken = new AccessToken(
                ['access_token' => 'mock-token'],
            );

            $setup->pkceCodeCookieHandler->expects(
                'getCookieFromRequest',
            )->with($request)->andReturn(new Cookie(
                'pkce',
                'mock-pkce-code',
            ));

            $setup->stateCookieHandler->expects(
                'getCookieFromRequest',
            )->with($request)->andReturn(new Cookie(
                'state',
                'mock-state-cookie',
            ));

            $setup->provider->expects('setPkceCode')->with(
                'mock-pkce-code',
            );

            $setup->provider->expects('getAccessToken')
                ->with('authorization_code', ['code' => 'mock-code'])
                ->andReturn($accessToken);

            $result = $setup->sut->create($params, $request);

            expect($result)->toBeInstanceOf(
                GetCallbackRespondWithAccessTokens::class,
            );
        },
    );

    it(
        'returns an invalid state responder on validation failure',
        function (
            string $code,
            string $state,
            string|null $pkce,
            string|null $stateCookie,
        ): void {
            $setup = new GetCallbackResponderFactoryTestSetup();

            $request = Mockery::mock(ServerRequestInterface::class);

            $params = new QueryParams($code, $state);

            $setup->pkceCodeCookieHandler->expects(
                'getCookieFromRequest',
            )->with($request)->andReturn(new Cookie(
                'pkce',
                $pkce,
            ));

            $setup->stateCookieHandler->expects(
                'getCookieFromRequest',
            )->with($request)->andReturn(new Cookie(
                'state',
                $stateCookie,
            ));

            $result = $setup->sut->create($params, $request);

            expect($result)->toBe(
                $setup->respondWithInvalidState,
            );
        },
    )->with([
        'empty code' => [
            'code' => '',
            'state' => 's',
            'pkce' => 'p',
            'stateCookie' => 'sc',
        ],
        'empty state' => [
            'code' => 'c',
            'state' => '',
            'pkce' => 'p',
            'stateCookie' => 'sc',
        ],
        'null pkce' => [
            'code' => 'c',
            'state' => 's',
            'pkce' => null,
            'stateCookie' => 'sc',
        ],
        'empty pkce' => [
            'code' => 'c',
            'state' => 's',
            'pkce' => '',
            'stateCookie' => 'sc',
        ],
        'null state cookie' => [
            'code' => 'c',
            'state' => 's',
            'pkce' => 'p',
            'stateCookie' => null,
        ],
        'empty state cookie' => [
            'code' => 'c',
            'state' => 's',
            'pkce' => 'p',
            'stateCookie' => '',
        ],
    ]);

    it(
        'returns invalid state responder when provider throws exception',
        function (): void {
            $setup = new GetCallbackResponderFactoryTestSetup();

            $request = Mockery::mock(ServerRequestInterface::class);

            $params = new QueryParams('mock-code', 'mock-state');

            $setup->pkceCodeCookieHandler->expects(
                'getCookieFromRequest',
            )->with($request)->andReturn(new Cookie(
                'pkce',
                'mock-pkce-code',
            ));

            $setup->stateCookieHandler->expects(
                'getCookieFromRequest',
            )->with($request)->andReturn(new Cookie(
                'state',
                'mock-state-cookie',
            ));

            $setup->provider->expects('setPkceCode')->with(
                'mock-pkce-code',
            );

            $setup->provider->expects('getAccessToken')
                ->andThrow(new Exception(
                    'Provider failed',
                ));

            $result = $setup->sut->create($params, $request);

            expect($result)->toBe(
                $setup->respondWithInvalidState,
            );
        },
    );

    it(
        'returns custom invalid state responder on validation failure',
        function (): void {
            $setup = new GetCallbackResponderFactoryTestSetup(
                withCustomInvalidStateResponder: true,
            );

            $request = Mockery::mock(ServerRequestInterface::class);

            $params = new QueryParams('', '');

            $setup->pkceCodeCookieHandler->allows(
                'getCookieFromRequest',
            )->andReturn(new Cookie('pkce', null));

            $setup->stateCookieHandler->allows(
                'getCookieFromRequest',
            )->andReturn(new Cookie('state', null));

            $result = $setup->sut->create($params, $request);

            expect($result)->toBe(
                $setup->customRespondWithInvalidState,
            );
        },
    );

    it(
        'returns custom invalid state responder when provider throws exception',
        function (): void {
            $setup = new GetCallbackResponderFactoryTestSetup(
                withCustomInvalidStateResponder: true,
            );

            $request = Mockery::mock(ServerRequestInterface::class);

            $params = new QueryParams('mock-code', 'mock-state');

            $setup->pkceCodeCookieHandler->expects(
                'getCookieFromRequest',
            )->with($request)->andReturn(new Cookie(
                'pkce',
                'mock-pkce-code',
            ));

            $setup->stateCookieHandler->expects(
                'getCookieFromRequest',
            )->with($request)->andReturn(new Cookie(
                'state',
                'mock-state-cookie',
            ));

            $setup->provider->expects('setPkceCode')->with(
                'mock-pkce-code',
            );

            $setup->provider->expects('getAccessToken')->andThrow(
                new Exception('Provider failed'),
            );

            $result = $setup->sut->create($params, $request);

            expect($result)->toBe(
                $setup->customRespondWithInvalidState,
            );
        },
    );
});
