<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\TokenRefresh;

use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;
use RxAnte\OAuth\Handlers\Common\ProviderOptionsReader;
use RxAnte\OAuth\Handlers\FusionAuth\FusionAuthConfig;
use RxAnte\OAuth\Handlers\FusionAuth\WellKnownRepository;
use RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken;
use Throwable;

use function json_decode;

readonly class GetRefreshedAccessTokenFromFusionAuth implements GetRefreshedAccessToken
{
    public function __construct(
        private FusionAuthConfig $config,
        private ClientFactory $clientFactory,
        private WellKnownRepository $wellKnownRepository,
        private ProviderOptionsReader $providerOptionsReader,
    ) {
    }

    public function get(AccessTokenInterface $accessToken): AccessTokenInterface|null
    {
        try {
            $refreshResponse = $this->getResponse($accessToken);

            if ($refreshResponse->getStatusCode() !== 200) {
                return null;
            }

            $refreshedJson = (array) json_decode(
                (string) $refreshResponse->getBody(),
                true,
            );

            $tmp = new AccessToken([
                /** @phpstan-ignore-next-line */
                'access_token' => (string) $refreshedJson['access_token'],
                /** @phpstan-ignore-next-line */
                'refresh_token' => (string) $refreshedJson['refresh_token'],
                /** @phpstan-ignore-next-line */
                'expires_in' => (int) $refreshedJson['expires_in'],
            ]);

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

        return $this->clientFactory->create()->post(
            $wellKnown->tokenEndpoint,
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::HEADERS => ['Content-Type' => 'application/x-www-form-urlencoded'],
                RequestOptions::VERIFY => $this->config->sslVerify,
                RequestOptions::FORM_PARAMS => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'client_id' => $this->providerOptionsReader->clientId(),
                    'client_secret' => $this->providerOptionsReader->clientSecret(),
                ],
            ],
        );
    }
}
