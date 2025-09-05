<?php

declare(strict_types=1);

use RxAnte\OAuth\Request\Payload;

describe('Payload', function (): void {
    uses()->group('Payload');

    it(
        'constructs with empty payload',
        function (): void {
            $sut = new Payload();

            expect($sut->prepareForRequest())->toBeNull();
        },
    );

    it(
        'constructs with specified payload',
        function (): void {
            $sut = new Payload([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]);

            expect($sut->prepareForRequest())->toBe(
                '{"foo":"bar","baz":"asdf"}',
            );
        },
    );

    it(
        'adds payload items',
        function (): void {
            $sut = new Payload([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]);

            $sut2 = $sut->withItem('baz', 'foo');

            $sut3 = $sut->withItem('mock', 'spy');

            expect($sut->prepareForRequest())->toBe(
                '{"foo":"bar","baz":"asdf"}',
            );

            expect($sut2->prepareForRequest())->toBe(
                '{"foo":"bar","baz":"foo"}',
            );

            expect($sut3->prepareForRequest())->toBe(
                '{"foo":"bar","baz":"asdf","mock":"spy"}',
            );
        },
    );

    it(
        'removes payload items',
        function (): void {
            $sut = (new Payload([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]))->withoutItem('foo');

            $sut2 = $sut->withoutItem('baz');

            expect($sut->prepareForRequest())->toBe(
                '{"baz":"asdf"}',
            );

            expect($sut2->prepareForRequest())->toBeNull();
        },
    );

    it(
        'replaces payload',
        function (): void {
            $sut = new Payload([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]);

            $sut2 = $sut->withPayload([
                'baz' => 'fdsa',
                'a' => 'b',
            ]);

            expect($sut->prepareForRequest())->toBe(
                '{"foo":"bar","baz":"asdf"}',
            );

            expect($sut2->prepareForRequest())->toBe(
                '{"baz":"fdsa","a":"b"}',
            );
        },
    );

    it(
        'adds multiple payload items at once',
        function (): void {
            $sut = new Payload([
                'foo' => 'bar',
                'baz' => 'asdf',
            ]);

            $sut2 = $sut->withAddedPayload([
                'baz' => 'fdsa',
                'a' => 'b',
            ]);

            expect($sut->prepareForRequest())->toBe(
                '{"foo":"bar","baz":"asdf"}',
            );

            expect($sut2->prepareForRequest())->toBe(
                '{"baz":"fdsa","a":"b"}',
            );
        },
    );
});
