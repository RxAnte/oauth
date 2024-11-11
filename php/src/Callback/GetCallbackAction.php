<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Callback;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Routes\RequestMethod;
use RxAnte\OAuth\Routes\Route;

readonly class GetCallbackAction
{
    public static function createRoute(): Route
    {
        return new Route(
            RequestMethod::GET,
            '/auth/callback/auth0',
            self::class,
        );
    }

    public function __construct(
        private QueryParamsFactory $queryParamsFactory,
        private GetCallbackResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $params = $this->queryParamsFactory->createFromRequest(
            $request,
        );

        $responder = $this->responderFactory->create(
            $params,
            $request,
        );

        return $responder->respond($request, $response);
    }
}
