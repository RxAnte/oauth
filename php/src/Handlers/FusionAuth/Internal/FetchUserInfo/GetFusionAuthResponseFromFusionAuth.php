<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use RxAnte\OAuth\Handlers\FusionAuth\FusionAuthConfig;
use RxAnte\OAuth\Handlers\FusionAuth\WellKnownRepository;
use RxAnte\OAuth\UserInfo\Jwt;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class GetFusionAuthResponseFromFusionAuth implements GetFusionAuthResponse
{
    public function __construct(
        private FusionAuthConfig $config,
        private ClientFactory $clientFactory,
        private WellKnownRepository $wellKnownRepository,
    ) {
    }

    public function get(Jwt $jwt): FusionAuthResponse
    {
        $wellKnown = $this->wellKnownRepository->get();

        $response = $this->clientFactory->create()->get(
            $wellKnown->userinfoEndpoint,
            [
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => $jwt->rawToken,
                ],
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::VERIFY => $this->config->sslVerify,
            ],
        );

        return new FusionAuthResponse(
            $response->getStatusCode(),
            (string) $response->getBody(),
        );
    }
}
