<?php

declare(strict_types=1);

use League\OAuth2\Client\Provider\AbstractProvider as OauthClientProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\SendToLogInCookieChain;
use RxAnte\OAuth\SendToLoginCreateAuthUrlEvent;
use RxAnte\OAuth\SendToLoginResponseFactory;

describe('SendToLoginResponseFactory', function (): void {
    uses()->group('SendToLoginResponseFactory');

    it(
        'creates a 302 response with correct Location header and sets cookies',
        function (): void {
            $mockOauthClientProvider = Mockery::mock(
                OauthClientProvider::class,
            );
            $mockOauthClientProvider
                ->shouldReceive('getAuthorizationUrl')
                ->once()
                ->andReturn('https://auth.example.com/oauth?foo=bar');
            $mockOauthClientProvider
                ->shouldReceive('getPkceCode')
                ->once()
                ->andReturn('pkce-code');
            $mockOauthClientProvider
                ->shouldReceive('getState')
                ->once()
                ->andReturn('state-xyz');

            $mockResponse = Mockery::mock(ResponseInterface::class);
            $mockResponse->shouldReceive('withStatus')
                ->with(302)
                ->andReturnSelf();
            $mockResponse->shouldReceive('withHeader')
                ->with(
                    'Location',
                    'https://auth.example.com/oauth?foo=bar',
                )
                ->andReturnSelf();

            $mockResponseFactory = Mockery::mock(
                ResponseFactoryInterface::class,
            );
            $mockResponseFactory
                ->shouldReceive('createResponse')
                ->once()
                ->andReturn($mockResponse);

            $mockCookieChain = Mockery::mock(
                SendToLogInCookieChain::class,
            );
            $mockCookieChain->shouldReceive('set')
                ->once()
                ->andReturn($mockResponse);

            $mockRequest = Mockery::mock(ServerRequestInterface::class);

            $mockEventDispatcher = Mockery::mock(
                EventDispatcherInterface::class,
            );
            $mockEventDispatcher->shouldReceive('dispatch')
                ->once()
                ->andReturnUsing(
                    function (SendToLoginCreateAuthUrlEvent $event) {
                        return $event;
                    },
                );

            $mockContainer = Mockery::mock(ContainerInterface::class);
            $mockContainer->shouldReceive('get')
                ->with(EventDispatcherInterface::class)
                ->andReturn($mockEventDispatcher);

            $factory = new SendToLoginResponseFactory(
                $mockContainer,
                $mockOauthClientProvider,
                $mockResponseFactory,
                $mockCookieChain,
            );

            $response = $factory->create($mockRequest);

            expect($response)->toBe($mockResponse);
        },
    );

    it(
        'creates a 302 response when EventDispatcherInterface is missing',
        function (): void {
            $mockOauthClientProvider = Mockery::mock(
                OauthClientProvider::class,
            );
            $mockOauthClientProvider
                ->shouldReceive('getAuthorizationUrl')
                ->once()
                ->andReturn('https://auth.example.com/oauth?foo=bar');
            $mockOauthClientProvider
                ->shouldReceive('getPkceCode')
                ->once()
                ->andReturn('pkce-code');
            $mockOauthClientProvider->shouldReceive('getState')
                ->once()
                ->andReturn('state-xyz');

            $mockResponse = Mockery::mock(ResponseInterface::class);
            $mockResponse->shouldReceive('withStatus')
                ->with(302)
                ->andReturnSelf();
            $mockResponse->shouldReceive('withHeader')
                ->with(
                    'Location',
                    'https://auth.example.com/oauth?foo=bar',
                )
                ->andReturnSelf();

            $mockResponseFactory = Mockery::mock(
                ResponseFactoryInterface::class,
            );
            $mockResponseFactory
                ->shouldReceive('createResponse')
                ->once()
                ->andReturn($mockResponse);

            $mockCookieChain = Mockery::mock(SendToLogInCookieChain::class);
            $mockCookieChain->shouldReceive('set')
                ->once()
                ->andReturn($mockResponse);

            $mockRequest = Mockery::mock(ServerRequestInterface::class);

            $mockContainer = Mockery::mock(ContainerInterface::class);
            $mockContainer->shouldReceive('get')
                ->with(EventDispatcherInterface::class)
                ->andThrow(new Exception('Not found'));

            $factory = new SendToLoginResponseFactory(
                $mockContainer,
                $mockOauthClientProvider,
                $mockResponseFactory,
                $mockCookieChain,
            );

            $response = $factory->create($mockRequest);

            expect($response)->toBe($mockResponse);
        },
    );
});
