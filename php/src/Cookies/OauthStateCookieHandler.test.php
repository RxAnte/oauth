<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Tests\Cookies;

use Mockery;
use Psr\Clock\ClockInterface;
use RxAnte\OAuth\Cookies\OauthStateCookieHandler;

use function describe;
use function expect;
use function it;
use function uses;

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
