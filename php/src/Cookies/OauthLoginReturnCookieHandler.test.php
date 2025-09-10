<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Tests\Cookies;

use Mockery;
use Psr\Clock\ClockInterface;
use RxAnte\OAuth\Cookies\OauthLoginReturnCookieHandler;

use function describe;
use function expect;
use function it;
use function uses;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

describe('OauthLoginReturnCookieHandler', function (): void {
    uses()->group('OauthLoginReturnCookieHandler');

    it('returns the correct cookie name', function (): void {
        $sut = new OauthLoginReturnCookieHandler(
            clock: Mockery::mock(ClockInterface::class),
        );

        $result = $sut->getCookieName();

        expect($result)->toBe('oauth-login-return');
    });
});
