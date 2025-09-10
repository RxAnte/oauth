<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use RxAnte\OAuth\Callback\GetCallbackRespondWithInvalidState;

describe('GetCallbackRespondWithInvalidState', function (): void {
    uses()->group('GetCallbackRespondWithInvalidState');

    it('create response for invalid state', function (): void {
        $body = Mockery::mock(StreamInterface::class);
        $body->expects('write')->with('Invalid state')
            ->andReturn(strlen('Invalid state'));

        $response2 = Mockery::mock(ResponseInterface::class);
        $response2->expects('getBody')->andReturn($body);

        $response1 = Mockery::mock(ResponseInterface::class);
        $response1->expects('withStatus')
            ->with(400)
            ->andReturn($response2);

        $sut = new GetCallbackRespondWithInvalidState();

        $response = $sut->respond(
            request: Mockery::mock(ServerRequestInterface::class),
            response: $response1,
        );

        expect($response)->toBe($response2);
    });
});
