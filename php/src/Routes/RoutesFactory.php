<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Routes;

use RxAnte\OAuth\Callback\GetCallbackAction;

readonly class RoutesFactory
{
    public function create(): RouteCollection
    {
        return new RouteCollection([
            GetCallbackAction::createRoute(),
        ]);
    }
}
