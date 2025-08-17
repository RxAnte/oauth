<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use RxAnte\OAuth\Handlers\Auth0\Auth0Config;
use RxAnte\OAuth\UserInfo\Jwt;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class GetAuth0ResponseFromAuth0 implements GetAuth0Response
{
    public function __construct(
        private Auth0Config $config,
        private ClientFactory $clientFactory,
    ) {
    }

    public function get(Jwt $jwt): Auth0Response
    {
        $response = $this->clientFactory->create()->get(
            $this->config->userInfoUrl,
            [
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => $jwt->rawToken,
                ],
                RequestOptions::HTTP_ERRORS => false,
            ],
        );

        return new Auth0Response(
            $response->getStatusCode(),
            (string) $response->getBody(),
        );
    }
}
