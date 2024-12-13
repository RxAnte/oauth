<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Routes;

use RxAnte\OAuth\Callback\GetCallbackAction;

readonly class RoutesFactory
{
    public function __construct(private string $pattern = '/auth/callback')
    {
    }

    public function create(): RouteCollection
    {
        return new RouteCollection([
            GetCallbackAction::createRoute($this->pattern),
        ]);
    }
}
