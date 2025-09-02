<?php

declare(strict_types=1);

use RxAnte\OAuth\Url;

describe('Url', function (): void {
    uses()->group('Url');

    it('can convert to string', function (): void {
        $url = new Url('https://example.com/path?foo=bar');
        expect($url->toString())->toBe(
            'https://example.com/path?foo=bar',
        );
    });

    it('can add a query param', function (): void {
        $url    = new Url('https://example.com');
        $newUrl = $url->withQueryParam('foo', 'bar');
        expect($newUrl->getQuery())->toBe('foo=bar');
    });

    it('can remove a query param', function (): void {
        $url    = new Url('https://example.com?foo=bar&baz=qux');
        $newUrl = $url->withoutQueryParam('foo');
        expect($newUrl->getQuery())->toBe('baz=qux');
    });

    it('can set only one query param', function (): void {
        $url    = new Url('https://example.com?foo=bar&baz=qux');
        $newUrl = $url->withOnlyQueryParam('baz', 'qux');
        expect($newUrl->getQuery())->toBe('baz=qux');
    });

    it('can set multiple query params', function (): void {
        $url    = new Url('https://example.com');
        $newUrl = $url->withQueryParams(
            ['foo' => 'bar', 'baz' => 'qux'],
        );
        expect($newUrl->getQuery())->toBe('foo=bar&baz=qux');
    });

    it(
        'replaces all query params with only the provided ones',
        function (): void {
            $url    = new Url('https://example.com?foo=bar&baz=qux');
            $newUrl = $url->withOnlyQueryParams(
                ['alpha' => 'beta', 'gamma' => 'delta'],
            );
            expect($newUrl->getQuery())->toBe(
                'alpha=beta&gamma=delta',
            );
        },
    );
});
