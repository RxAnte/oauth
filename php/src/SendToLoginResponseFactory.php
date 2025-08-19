<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider as OauthClientProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\SendToLogInCookieChain;
use Throwable;

readonly class SendToLoginResponseFactory
{
    private EventDispatcherInterface|null $eventDispatcher;

    public function __construct(
        ContainerInterface $di,
        private OauthClientProvider $provider,
        private ResponseFactoryInterface $responseFactory,
        private SendToLogInCookieChain $sendToLogInCookieChain,
    ) {
        try {
            $this->eventDispatcher = $di->get(EventDispatcherInterface::class);
        } catch (Throwable) {
            $this->eventDispatcher = null;
        }
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * It is important to make this call first, otherwise `getPkceCode` and
         * `getState` won't work correctly (not a "stateless" service,
         * unfortunately)
         */
        $authUrlString = $this->provider->getAuthorizationUrl();

        $authUrl = new Url($authUrlString);

        $event = new SendToLoginCreateAuthUrlEvent(
            $request,
            $authUrl,
        );

        $this->eventDispatcher?->dispatch($event);

        $response = $this->sendToLogInCookieChain->set(
            request: $request,
            response: $this->responseFactory->createResponse(),
            oauthPkceCode: (string) $this->provider->getPkceCode(),
            oauthState: $this->provider->getState(),
        );

        return $response->withStatus(302)
            ->withHeader('Location', $event->url()->toString());
    }
}
