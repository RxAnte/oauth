<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

use Dflydev\FigCookies\Cookie;
use Dflydev\FigCookies\Cookies;
use GuzzleHttp\Psr7\Response;
use Mockery\MockInterface;
use Psr\Clock\ClockInterface;
use Psr\Http\Message\RequestInterface;
use RxAnte\OAuth\Cookies\AbstractLoginCookieHandler;

describe('AbstractLoginCookieHandler', function (): void {
    uses()->group('AbstractLoginCookieHandler');

    readonly class TestableLoginCookieHandler extends AbstractLoginCookieHandler
    {
        public function getCookieName(): string
        {
            return 'test-cookie';
        }
    }

    readonly class AbstractLoginCookieHandlerTestSetup
    {
        public TestableLoginCookieHandler $sut;
        public MockInterface&ClockInterface $clock;
        public DateTimeImmutable $now;

        public function __construct()
        {
            $this->clock = Mockery::mock(ClockInterface::class);

            $this->now = new DateTimeImmutable(
                '2023-01-01T12:00:00Z',
            );

            $this->clock->allows('now')->andReturn($this->now);

            $this->sut = new TestableLoginCookieHandler(
                clock: $this->clock,
            );
        }
    }

    it(
        'returns the cookie expiration as 5 months from now',
        function (): void {
            $setup = new AbstractLoginCookieHandlerTestSetup();

            $expectedExpiration = $setup->now->add(
                new DateInterval('P5M'),
            );

            $result = $setup->sut->getCookieExpiration();

            expect($result)->toEqual($expectedExpiration);
        },
    );

    it(
        'gets a cookie from the request',
        function (): void {
            $setup = new AbstractLoginCookieHandlerTestSetup();

            $request = Mockery::mock(RequestInterface::class);
            $request->allows('getHeaderLine')
                ->with(Cookies::COOKIE_HEADER)
                ->andReturn('test-cookie=mock-cookie-value');

            $cookie = $setup->sut->getCookieFromRequest($request);

            expect($cookie)->toBeInstanceOf(Cookie::class);

            expect($cookie->getName())->toBe('test-cookie');

            expect($cookie->getValue())->toBe(
                'mock-cookie-value',
            );
        },
    );

    it('sets a cookie on the response', function (): void {
        $setup = new AbstractLoginCookieHandlerTestSetup();

        $response = new Response();

        $newResponse = $setup->sut->setResponseCookie(
            $response,
            'mock-cookie-value',
        );

        $expectedExpiration = $setup->now
            ->add(new DateInterval('P5M'))
            ->format(DateTimeImmutable::RFC7231);

        expect($newResponse->getHeaderLine('Set-Cookie'))
            ->toBe(implode('; ', [
                'test-cookie=mock-cookie-value',
                'Path=/',
                'Expires=' . $expectedExpiration,
            ]));
    });
});
