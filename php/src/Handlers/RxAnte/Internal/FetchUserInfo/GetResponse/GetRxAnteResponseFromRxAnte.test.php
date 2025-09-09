<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Tests\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponseFromRxAnte;
use RxAnte\OAuth\Handlers\RxAnte\WellKnown;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownRepository;

use function describe;
use function expect;
use function it;
use function uses;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('GetRxAnteResponseFromRxAnte', function (): void {
    uses()->group('GetRxAnteResponseFromRxAnte');

    it(
        'gets a response from the userinfo endpoint',
        function (): void {
            $jwt = Mockery::mock(JwtToken::class);
            $jwt->expects('toString')->andReturn('mock-jwt-string');

            $responseBody = Mockery::mock(StreamInterface::class);
            $responseBody->expects('__toString')->andReturn(
                '{"sub":"123"}',
            );

            $response = Mockery::mock(ResponseInterface::class);
            $response->expects('getStatusCode')->andReturn(200);
            $response->expects('getBody')->andReturn($responseBody);

            $guzzleClient = Mockery::mock(Client::class);
            $guzzleClient->expects('get')
                ->with(
                    'https://userinfo',
                    [
                        RequestOptions::HEADERS => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer mock-jwt-string',
                        ],
                        RequestOptions::HTTP_ERRORS => false,
                    ],
                )
                ->andReturn($response);

            $clientFactory = Mockery::mock(ClientFactory::class);
            $clientFactory->expects('create')->andReturn(
                $guzzleClient,
            );

            $wellKnownRepository = Mockery::mock(
                WellKnownRepository::class,
            );
            $wellKnownRepository->expects('get')->andReturn(
                new WellKnown(
                    issuer: 'https://issuer',
                    authorizationEndpoint: 'https://auth',
                    tokenEndpoint: 'https://token',
                    userinfoEndpoint: 'https://userinfo',
                ),
            );

            $sut = new GetRxAnteResponseFromRxAnte(
                clientFactory: $clientFactory,
                wellKnownRepository:$wellKnownRepository,
            );

            $result = $sut->get($jwt);

            expect($result->isFromCache)->toBeFalse()
            ->and($result->response->statusCode)->toBe(200)
            ->and($result->response->body)->toBe('{"sub":"123"}');
        },
    );
});
