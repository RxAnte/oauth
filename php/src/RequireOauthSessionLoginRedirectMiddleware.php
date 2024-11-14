<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider as OauthClientProvider;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\Cookies\SendToLogInCookieChain;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

readonly class RequireOauthSessionLoginRedirectMiddleware implements MiddlewareInterface
{
    public function __construct(
        private OauthClientProvider $provider,
        private ResponseFactoryInterface $responseFactory,
        private SendToLogInCookieChain $sendToLogInCookieChain,
        private OauthUserInfoRepositoryInterface $userInfoRepository,
        private CustomAuthenticationHookFactory $authHookFactory,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $userInfo = $this->userInfoRepository->getUserInfoFromRequestSession(
            $request,
        );

        if (! $userInfo->isValid) {
            return $this->sendToLogIn($request);
        }

        $request = $request->withAttribute(
            'oauthUserInfo',
            $userInfo,
        );

        $customAuth = $this->authHookFactory->create()->process(
            userInfo: $userInfo,
            request: $request,
            defaultAccessDeniedResponse: $this->sendToLogIn($request),
        );

        $request = $customAuth->request ?? $request;

        if ($customAuth->response !== null) {
            return $customAuth->response;
        }

        return $handler->handle($request);
    }

    private function sendToLogIn(
        ServerRequestInterface $request,
    ): ResponseInterface {
        /**
         * It is important to make this call first, otherwise `getPkceCode` and
         * `getState` won't work correctly (not a "stateless" service,
         * unfortunately)
         */
        $authorizationUrl = $this->provider->getAuthorizationUrl();

        $response = $this->sendToLogInCookieChain->set(
            request: $request,
            response: $this->responseFactory->createResponse(),
            oauthPkceCode: (string) $this->provider->getPkceCode(),
            oauthState: $this->provider->getState(),
        );

        return $response->withStatus(302)
            ->withHeader('Location', $authorizationUrl);
    }
}
