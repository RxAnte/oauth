<?php

declare(strict_types=1);

use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;

describe('RxAnteConfig', function (): void {
    uses()->group('RxAnteConfig');

    it('works with default values', function (): void {
        $config = new RxAnteConfig(wellKnownUrl: 'https://well-known/foo');

        expect($config->wellKnownUrl)->toBe(
            'https://well-known/foo',
        );

        expect($config->wellKnownCacheKey)->toBe(
            'rxante_auth_well_known',
        );
        $dateInterval = $config->wellKnownCacheExpiresAfter;

        /** @phpstan-ignore-next-line */
        $totalSeconds = ($dateInterval->days * 24 * 60 * 60)
            + ($dateInterval->h * 60 * 60)
            + ($dateInterval->i * 60)
            + $dateInterval->s;
        expect($totalSeconds)->toBe(86400);

        $testM2M = $config->m2mSubjectIsAuthorized('mock-subject');
        expect($testM2M)->toBeFalse();
    });

    it('works with provided values', function (): void {
        $config = new RxAnteConfig(
            wellKnownUrl: 'https://foo/well-known',
            wellKnownCacheKey: 'foo-cache-key',
            wellKnownCacheExpiresAfter: new DateInterval('PT2H'),
            m2mAuthorizedSubjects: ['foo-sub-1', 'foo-sub-2'],
        );

        expect($config->wellKnownUrl)->toBe(
            'https://foo/well-known',
        );

        expect($config->wellKnownCacheKey)->toBe(
            'foo-cache-key',
        );
        $dateInterval = $config->wellKnownCacheExpiresAfter;

        /** @phpstan-ignore-next-line */
        $totalSeconds = ($dateInterval->days * 24 * 60 * 60)
            + ($dateInterval->h * 60 * 60)
            + ($dateInterval->i * 60)
            + $dateInterval->s;
        expect($totalSeconds)->toBe(7200);

        $testM2M = $config->m2mSubjectIsAuthorized('mock');
        expect($testM2M)->toBeFalse();

        $testM2M = $config->m2mSubjectIsAuthorized('mock@foo-sub-1');
        expect($testM2M)->toBeFalse();

        $testM2M = $config->m2mSubjectIsAuthorized('foo-sub-1');
        expect($testM2M)->toBeTrue();

        $testM2M = $config->m2mSubjectIsAuthorized('foo-sub-1@foo');
        expect($testM2M)->toBeTrue();

        $testM2M = $config->m2mSubjectIsAuthorized('foo-sub-2');
        expect($testM2M)->toBeTrue();

        $testM2M = $config->m2mSubjectIsAuthorized('foo-sub-2@bar');
        expect($testM2M)->toBeTrue();
    });
});
