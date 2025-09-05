<?php

declare(strict_types=1);

use RxAnte\OAuth\Request\QueryParams;

describe('QueryParams', function (): void {
    uses()->group('QueryParams');

    it(
        'constructs with empty params',
        function (): void {
            $sut = new QueryParams();

            expect($sut->asQueryString())->toBe('');
        },
    );

    it(
        'constructs with specified params',
        function (): void {
            $sut = new QueryParams([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]);

            expect($sut->asQueryString())->toBe(
                '?foo=bar&baz=asdf',
            );
        },
    );

    it(
        'adds params',
        function (): void {
            $sut = new QueryParams([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]);

            $sut2 = $sut->withParam('baz', 'fdsa');

            $sut3 = $sut->withParam('k', 'v');

            expect($sut->asQueryString())->toBe(
                '?foo=bar&baz=asdf',
            );

            expect($sut2->asQueryString())->toBe(
                '?foo=bar&baz=fdsa',
            );

            expect($sut3->asQueryString())->toBe(
                '?foo=bar&baz=asdf&k=v',
            );
        },
    );

    it(
        'removes params',
        function (): void {
            $sut = (new QueryParams([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]))->withoutParam('foo');

            $sut2 = $sut->withoutParam('baz');

            expect($sut->asQueryString())->toBe('?baz=asdf');

            expect($sut2->asQueryString())->toBe('');
        },
    );

    it(
        'replaces params',
        function (): void {
            $sut = new QueryParams([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]);

            $sut2 = $sut->withParams([
                'baz' => 'fdsa',
                'a' => 'b',
            ]);

            expect($sut->asQueryString())->toBe(
                '?foo=bar&baz=asdf',
            );

            expect($sut2->asQueryString())->toBe(
                '?baz=fdsa&a=b',
            );
        },
    );

    it(
        'adds multiple params at once',
        function (): void {
            $sut = new QueryParams([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]);

            $sut2 = $sut->withAddedParams([
                'baz' => 'fdsa',
                'a' => 'b',
            ]);

            expect($sut->asQueryString())->toBe(
                '?foo=bar&baz=asdf',
            );

            expect($sut2->asQueryString())->toBe(
                '?foo=bar&baz=fdsa&a=b',
            );
        },
    );
});
