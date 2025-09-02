<?php

declare(strict_types=1);

describe('json_validate', function (): void {
    uses()->group('json_validate');

    it('validates correct JSON', function (): void {
        expect(json_validate('{"foo": "bar"}'))->toBeTrue();
    });

    it('invalidates incorrect JSON', function (): void {
        expect(json_validate('{"foo": "bar"'))->toBeFalse();
    });

    it('throws on invalid depth', function (): void {
        json_validate('{}', 0);
    })->throws(ValueError::class);

    it('throws on too large depth', function (): void {
        json_validate('{}', JSON_MAX_DEPTH + 1);
    })->throws(ValueError::class);

    it('throws on invalid flags', function (): void {
        json_validate('{}', 512, 123);
    })->throws(ValueError::class);
});
