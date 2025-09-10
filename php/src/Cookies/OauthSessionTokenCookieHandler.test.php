<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Tests\Cookies;

use Mockery;
use Psr\Clock\ClockInterface;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;

use function describe;
use function expect;
use function it;
use function uses;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

describe('OauthSessionTokenCookieHandler', function (): void {
    uses()->group('OauthSessionTokenCookieHandler');

    it(
        'returns the correct cookie name',
        function (): void {
            $sut = new OauthSessionTokenCookieHandler(
                clock: Mockery::mock(ClockInterface::class),
            );

            $result = $sut->getCookieName();

            expect($result)->toBe('oauth-session-token');
        },
    );

    it(
        'returns the correct cookie expiration',
        function (): void {
            $sut = new OauthSessionTokenCookieHandler(
                clock: Mockery::mock(ClockInterface::class),
            );

            $result = $sut->getCookieExpiration();

            expect($result)->toBeNull();
        },
    );
});
