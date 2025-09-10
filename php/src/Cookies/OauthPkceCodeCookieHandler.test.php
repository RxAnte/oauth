<?php

declare(strict_types=1);

use Psr\Clock\ClockInterface;
use RxAnte\OAuth\Cookies\OauthPkceCodeCookieHandler;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

describe('OauthPkceCodeCookieHandler', function (): void {
    uses()->group('OauthPkceCodeCookieHandler');

    it('returns the correct cookie name', function (): void {
        $sut = new OauthPkceCodeCookieHandler(
            clock: Mockery::mock(ClockInterface::class),
        );

        $result = $sut->getCookieName();

        expect($result)->toBe('oauth-pkce-code');
    });
});
