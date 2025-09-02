<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\SendToLoginCreateAuthUrlEvent;
use RxAnte\OAuth\Url;

describe('SendToLoginCreateAuthUrlEvent', function (): void {
    uses()->group('SendToLoginCreateAuthUrlEvent');

    it('returns the request object', function (): void {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);

        $mockUrl = Mockery::mock(Url::class);

        $event = new SendToLoginCreateAuthUrlEvent(
            $mockRequest,
            $mockUrl,
        );

        expect($event->request())->toBe($mockRequest);
    });

    it(
        'finds a cookie value if present',
        function (): void {
            $mockRequest = Mockery::mock(ServerRequestInterface::class);
            $mockRequest->shouldReceive('getCookieParams')
            ->andReturn(['foo' => 'bar']);

            $mockUrl = Mockery::mock(Url::class);

            $event = new SendToLoginCreateAuthUrlEvent(
                $mockRequest,
                $mockUrl,
            );

            expect($event->findCookieValue('foo'))
            ->toBe('bar');

            expect($event->findCookieValue('baz'))
                ->toBeNull();
        },
    );

    it(
        'finds a query param if present',
        function (): void {
            $mockRequest = Mockery::mock(ServerRequestInterface::class);
            $mockRequest->shouldReceive('getQueryParams')
            ->andReturn(['alpha' => 'beta']);

            $mockUrl = Mockery::mock(Url::class);

            $event = new SendToLoginCreateAuthUrlEvent(
                $mockRequest,
                $mockUrl,
            );

            expect($event->findQueryParam('alpha'))
            ->toBe('beta');

            expect($event->findQueryParam('gamma'))->toBeNull();
        },
    );

    it('returns and sets the url', function (): void {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);

        $mockUrl1 = Mockery::mock(Url::class);

        $mockUrl2 = Mockery::mock(Url::class);

        $event = new SendToLoginCreateAuthUrlEvent(
            $mockRequest,
            $mockUrl1,
        );

        expect($event->url())->toBe($mockUrl1);

        $event->setUrl($mockUrl2);

        expect($event->url())->toBe($mockUrl2);
    });
});
