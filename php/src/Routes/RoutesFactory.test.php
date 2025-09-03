<?php

declare(strict_types=1);

use RxAnte\OAuth\Routes\RouteCollection;
use RxAnte\OAuth\Routes\RoutesFactory;

describe('RoutesFactory', function (): void {
    uses()->group('RoutesFactory');

    it(
        'creates a RouteCollection with the default pattern',
        function (): void {
            $factory = new RoutesFactory();

            $collection = $factory->create();

            expect($collection)->toBeInstanceOf(
                RouteCollection::class,
            );

            $routes = $collection->routes;
            expect($routes)->toBeArray();
            expect($routes)->toHaveCount(1);

            $route = $routes[0];
            expect($route->pattern)->toBe('/auth/callback');
        },
    );

    it(
        'creates a RouteCollection with a custom pattern',
        function (): void {
            $factory    = new RoutesFactory('/custom/callback');
            $collection = $factory->create();

            $routes = $collection->routes;
            expect($routes)->toHaveCount(1);

            $route = $routes[0];
            expect($route->pattern)->toBe('/custom/callback');
        },
    );
});
