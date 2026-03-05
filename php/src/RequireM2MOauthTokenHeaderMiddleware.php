<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

use function json_encode;

/**
 * This middleware is designed to also run the RequireOauthTokenHeaderMiddleware
 * if needed so you don't need to have it on the middleware stack too
 */
class RequireM2MOauthTokenHeaderMiddleware implements
    MiddlewareInterface,
    RequestHandlerInterface
{
    public function __construct(
        private readonly RxAnteConfig $config,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly RequireOauthTokenHeaderMiddleware $requireOauthToken,
    ) {
    }

    private RequestHandlerInterface $currentHandler;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $this->currentHandler = $handler;

        /**
         * If the oauthUserInfo attribute has already been set, the
         * RequireOauthTokenHeaderMiddleware has already run and we don't
         * need to run it again.
         */
        $userInfo = $request->getAttribute('oauthUserInfo');
        if ($userInfo instanceof OauthUserInfo) {
            return $this->handle($request);
        }

        return $this->requireOauthToken->process(
            $request,
            $this,
        );
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userInfo = $request->getAttribute('oauthUserInfo');

        if (
            ! $userInfo instanceof OauthUserInfo ||
            ! $this->config->m2mSubjectIsAuthorized($userInfo->sub)
        ) {
            return $this->sendAccessDenied();
        }

        return $this->currentHandler->handle($request);
    }

    private function sendAccessDenied(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response = $response->withHeader(
            'Content-type',
            'application/json',
        );

        $response->getBody()->write((string) json_encode([
            'error' => 'access_denied',
            'error_description' => 'A valid M2M bearer token is required to access this resource',
            'message' => 'A valid M2M bearer token is required to access this resource',
        ]));

        return $response->withStatus(403);
    }
}
