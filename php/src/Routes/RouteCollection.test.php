<?php

declare(strict_types=1);

use RxAnte\OAuth\Routes\RequestMethod;
use RxAnte\OAuth\Routes\Route;
use RxAnte\OAuth\Routes\RouteCollection;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

describe('RouteCollection', function (): void {
    uses()->group('RouteCollection');

    readonly class RouteCollectionTestClassFoo
    {
    }

    readonly class RouteCollectionTestClassBar
    {
    }

    readonly class RouteCollectionTestSetup
    {
        public RouteCollection $sut;

        public function __construct()
        {
            $this->sut = new RouteCollection([
                new Route(
                    RequestMethod::GET,
                    '/foo',
                    RouteCollectionTestClassFoo::class,
                ),
                new Route(
                    RequestMethod::POST,
                    '/bar',
                    RouteCollectionTestClassBar::class,
                ),
            ]);
        }
    }

    it(
        'constructs and exposes routes as array',
        function (): void {
            $routes = (new RouteCollectionTestSetup())->sut->routes;

            expect($routes)->toBeArray();

            expect($routes)->toHaveCount(2);

            expect($routes[0]->pattern)->toBe('/foo');

            expect($routes[1]->pattern)->toBe('/bar');
        },
    );

    it('maps over routes', function (): void {
        $patterns = (new RouteCollectionTestSetup())->sut->map(
            fn ($r) => $r->pattern,
        );

        expect($patterns)->toBe(['/foo', '/bar']);
    });

    it(
        'returns routes as array of arrays',
        function (): void {
            $asArray = (new RouteCollectionTestSetup())->sut->asArray();

            expect($asArray)->toBe([
                [
                    'requestMethod' => 'GET',
                    'pattern' => '/foo',
                    'class' => 'RouteCollectionTestClassFoo',
                ],
                [
                    'requestMethod' => 'POST',
                    'pattern' => '/bar',
                    'class' => 'RouteCollectionTestClassBar',
                ],
            ]);
        },
    );

    it('filters routes', function (): void {
        $filtered = (new RouteCollectionTestSetup())->sut->filter(
            fn ($r) => $r->pattern === '/bar',
        );

        expect($filtered->routes)->toHaveCount(1);

        expect($filtered->routes[0]->pattern)->toBe('/bar');
    });

    it(
        'pluckClassName returns the correct route',
        function (): void {
            $route = (new RouteCollectionTestSetup())->sut->pluckClassName(
                'RouteCollectionTestClassBar',
            );

            expect($route->pattern)->toBe('/bar');
        },
    );

    it(
        'first returns the first route',
        function (): void {
            $first = (new RouteCollectionTestSetup())->sut->first();

            expect($first->pattern)->toBe('/foo');
        },
    );
});
