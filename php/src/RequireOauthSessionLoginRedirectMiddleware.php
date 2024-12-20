<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

readonly class RequireOauthSessionLoginRedirectMiddleware implements MiddlewareInterface
{
    public function __construct(
        private CustomAuthenticationHookFactory $authHookFactory,
        private OauthUserInfoRepositoryInterface $userInfoRepository,
        private SendToLoginResponseFactory $sendToLoginResponseFactory,
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
            return $this->sendToLoginResponseFactory->create($request);
        }

        $request = $request->withAttribute(
            'oauthUserInfo',
            $userInfo,
        );

        $customAuth = $this->authHookFactory->create()->process(
            userInfo: $userInfo,
            request: $request,
            defaultAccessDeniedResponse: $this->sendToLoginResponseFactory->create(
                $request,
            ),
        );

        $request = $customAuth->request ?? $request;

        if ($customAuth->response !== null) {
            return $customAuth->response;
        }

        return $handler->handle($request);
    }
}
