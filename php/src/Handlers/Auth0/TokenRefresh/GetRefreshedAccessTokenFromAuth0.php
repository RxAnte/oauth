<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\TokenRefresh;

use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;
use RxAnte\OAuth\Handlers\Auth0\WellKnownRepository;
use RxAnte\OAuth\Handlers\Common\ProviderOptionsReader;
use RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken;
use Throwable;

use function json_decode;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class GetRefreshedAccessTokenFromAuth0 implements GetRefreshedAccessToken
{
    public function __construct(
        private ClientFactory $clientFactory,
        private WellKnownRepository $wellKnownRepository,
        private ProviderOptionsReader $providerOptionsReader,
    ) {
    }

    public function get(
        AccessTokenInterface $accessToken,
    ): AccessTokenInterface|null {
        try {
            $refreshResponse = $this->getResponse($accessToken);

            if ($refreshResponse->getStatusCode() !== 200) {
                return null;
            }

            $refreshedJson = (array) json_decode(
                (string) $refreshResponse->getBody(),
                true,
            );

            return new AccessToken([
                /** @phpstan-ignore-next-line */
                'access_token' => (string) $refreshedJson['access_token'],
                /** @phpstan-ignore-next-line */
                'refresh_token' => (string) $refreshedJson['refresh_token'],
                /** @phpstan-ignore-next-line */
                'expires_in' => (int) $refreshedJson['expires_in'],
            ]);
        } catch (Throwable) {
            return null;
        }
    }

    private function getResponse(
        AccessTokenInterface $accessToken,
    ): ResponseInterface {
        $wellKnown = $this->wellKnownRepository->get();

        $tokenUrl = $wellKnown->tokenEndpoint;

        return $this->clientFactory->create()->post(
            $tokenUrl,
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
                RequestOptions::JSON => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'client_id' => $this->providerOptionsReader->clientId(),
                    'client_secret' => $this->providerOptionsReader->clientSecret(),
                ],
            ],
        );
    }
}
