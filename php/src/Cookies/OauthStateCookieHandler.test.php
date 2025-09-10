<?php

declare(strict_types=1);

use Psr\Clock\ClockInterface;
use RxAnte\OAuth\Cookies\OauthStateCookieHandler;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

describe('OauthStateCookieHandler', function (): void {
    uses()->group('OauthStateCookieHandler');

    it('returns the correct cookie name', function (): void {
        $sut = new OauthStateCookieHandler(
            clock: Mockery::mock(ClockInterface::class),
        );

        $result = $sut->getCookieName();

        expect($result)->toBe('oauth-state');
    });
});
