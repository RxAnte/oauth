<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RxAnte\OAuth\MachineToMachine\AccessTokenRequestConfig;
use RxAnte\OAuth\MachineToMachine\RequestAccessToken;

describe('RequestAccessToken', function (): void {
    uses()->group('RequestAccessToken');

    it(
        'returns empty access token when invalid json is returned',
        function (): void {
            $body = Mockery::mock(StreamInterface::class);
            $body->expects('getContents')->andReturn('noop');

            $response = Mockery::mock(ResponseInterface::class);
            $response->expects('getBody')->andReturn($body);

            $client = Mockery::mock(Client::class);
            $client->expects('post')
                ->with(
                    '',
                    [
                        RequestOptions::HEADERS => ['content-type' => 'application/json'],
                        'curl' => [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        ],
                        RequestOptions::BODY => json_encode([
                            'client_id' => '',
                            'client_secret' => '',
                            'audience' => '',
                            'grant_type' => 'client_credentials',
                        ]),
                    ],
                )
                ->andReturn($response);

            $clientFactory = Mockery::mock(ClientFactory::class);
            $clientFactory->expects('create')->andReturn($client);

            $sut = new RequestAccessToken(clientFactory: $clientFactory);

            $token = $sut->fetch(new AccessTokenRequestConfig(
                tokenUrl: '',
                clientId: '',
                clientSecret: '',
                audience: '',
            ));

            expect($token->accessToken)->toBe('');

            expect($token->scope)->toBe('');

            expect($token->expiresIn)->toBe(0);

            expect($token->tokenType)->toBe('');
        },
    );

    it(
        'returns empty access token when json does not contain expected key/values',
        function (): void {
            $body = Mockery::mock(StreamInterface::class);
            $body->expects('getContents')->andReturn(
                '{"foo": "bar"}',
            );

            $response = Mockery::mock(ResponseInterface::class);
            $response->expects('getBody')->andReturn($body);

            $client = Mockery::mock(Client::class);
            $client->expects('post')
                ->with(
                    '',
                    [
                        RequestOptions::HEADERS => ['content-type' => 'application/json'],
                        'curl' => [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        ],
                        RequestOptions::BODY => json_encode([
                            'client_id' => '',
                            'client_secret' => '',
                            'audience' => '',
                            'grant_type' => '',
                        ]),
                    ],
                )
                ->andReturn($response);

            $clientFactory = Mockery::mock(ClientFactory::class);
            $clientFactory->expects('create')->andReturn($client);

            $sut = new RequestAccessToken(clientFactory: $clientFactory);

            $token = $sut->fetch(new AccessTokenRequestConfig(
                tokenUrl: '',
                clientId: '',
                clientSecret: '',
                audience: '',
                grantType: '',
            ));

            expect($token->accessToken)->toBe('');

            expect($token->scope)->toBe('');

            expect($token->expiresIn)->toBe(0);

            expect($token->tokenType)->toBe('');
        },
    );

    it(
        'returns access token with expected values',
        function (): void {
            $body = Mockery::mock(StreamInterface::class);
            $body->expects('getContents')->andReturn(
                '{"access_token": "mock-access-token", "scope": "mock-scope", "expires_in": 123, "token_type": "mock-token-type"}',
            );

            $response = Mockery::mock(ResponseInterface::class);
            $response->expects('getBody')->andReturn($body);

            $client = Mockery::mock(Client::class);
            $client->expects('post')
                ->with(
                    'https://mock-token-url/',
                    [
                        RequestOptions::HEADERS => ['content-type' => 'application/json'],
                        'curl' => [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        ],
                        RequestOptions::BODY => json_encode([
                            'client_id' => 'mock-id',
                            'client_secret' => 'mock-secret',
                            'audience' => 'mock-audience',
                            'grant_type' => 'mock-grant-type',
                        ]),
                    ],
                )
                ->andReturn($response);

            $clientFactory = Mockery::mock(ClientFactory::class);
            $clientFactory->expects('create')->andReturn($client);

            $sut = new RequestAccessToken(clientFactory: $clientFactory);

            $token = $sut->fetch(new AccessTokenRequestConfig(
                tokenUrl: 'https://mock-token-url/',
                clientId: 'mock-id',
                clientSecret: 'mock-secret',
                audience: 'mock-audience',
                grantType: 'mock-grant-type',
            ));

            expect($token->accessToken)->toBe(
                'mock-access-token',
            );

            expect($token->scope)->toBe(
                'mock-scope',
            );

            expect($token->expiresIn)->toBe(123);

            expect($token->tokenType)->toBe('mock-token-type');
        },
    );
});
