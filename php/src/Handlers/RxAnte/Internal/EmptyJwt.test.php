<?php

declare(strict_types=1);

use RxAnte\OAuth\Handlers\RxAnte\Internal\EmptyJwt;

describe('EmptyJwt', function (): void {
    uses()->group('EmptyJwt');

    it('returns correct empty values', function (): void {
        $sut = new EmptyJwt();

        expect($sut->headers()->toString())->toBe('');

        expect($sut->isPermittedFor('foo'))->toBeFalse();

        expect($sut->isIdentifiedBy('foo'))->toBeFalse();

        expect($sut->isRelatedTo('foo'))->toBeFalse();

        expect($sut->hasBeenIssuedBy('foo', 'bar'))
            ->toBeFalse();

        $now = new DateTimeImmutable();

        expect($sut->hasBeenIssuedBefore($now))->toBeFalse();

        expect($sut->isMinimumTimeBefore($now))->toBeFalse();

        expect($sut->isExpired($now))->toBeTrue();

        expect($sut->toString())->toBe('noop');

        expect($sut->claims()->toString())->toBe('');

        expect($sut->signature()->toString())->toBe('noop');

        expect($sut->payload())->toBe('noop');
    });
});
