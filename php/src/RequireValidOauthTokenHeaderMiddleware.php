<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

use function json_encode;

readonly class RequireValidOauthTokenHeaderMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private OauthUserInfoRepositoryInterface $userInfoRepository,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $userInfo = $this->userInfoRepository->getUserInfoFromRequestToken(
            $request,
        );

        if (! $userInfo->isValid) {
            return $this->sendAccessDenied();
        }

        $request = $request->withAttribute(
            'oauthUserInfo',
            $userInfo,
        );

        return $handler->handle($request);
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
            'error_description' => 'A valid bearer token is required to access this resource',
            'message' => 'A valid bearer token is required to access this resource',
        ]));

        return $response->withStatus(401);
    }
}
