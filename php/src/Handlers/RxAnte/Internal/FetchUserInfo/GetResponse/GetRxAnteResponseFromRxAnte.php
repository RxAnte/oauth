<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse;

use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownRepository;

readonly class GetRxAnteResponseFromRxAnte implements GetRxAnteResponse
{
    public function __construct(
        private ClientFactory $clientFactory,
        private WellKnownRepository $wellKnownRepository,
    ) {
    }

    public function get(JwtToken $jwt): RxAnteResponseWrapper
    {
        $response = $this->clientFactory->create()->get(
            $this->wellKnownRepository->get()->userinfoEndpoint,
            [
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $jwt->toString(),
                ],
                RequestOptions::HTTP_ERRORS => false,
            ],
        );

        return new RxAnteResponseWrapper(new RxAnteResponse(
            $response->getStatusCode(),
            (string) $response->getBody(),
        ));
    }
}
