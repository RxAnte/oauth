<?php

declare(strict_types=1);

namespace RxAnte\OAuth\MachineToMachine;

use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;

use function is_int;
use function is_string;
use function json_decode;
use function json_encode;

use const CURL_HTTP_VERSION_1_1;
use const CURLOPT_ENCODING;
use const CURLOPT_HTTP_VERSION;
use const CURLOPT_MAXREDIRS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_TIMEOUT;

readonly class RequestAccessToken
{
    public function __construct(private ClientFactory $clientFactory)
    {
    }

    public function fetch(AccessTokenRequestConfig $config): AccessToken
    {
        $response = $this->clientFactory->create()->post(
            $config->tokenUrl,
            [
                RequestOptions::HEADERS => ['content-type' => 'application/json'],
                'curl' => [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                ],
                RequestOptions::BODY => json_encode(
                    $config->requestOptionsArray(),
                ),
            ],
        );

        $contents = (array) json_decode(
            $response->getBody()->getContents(),
            true,
        );

        $accessToken = $contents['access_token'] ?? '';

        $scope = $contents['scope'] ?? '';

        $expiresIn = $contents['expires_in'] ?? 0;

        $tokenType = $contents['token_type'] ?? '';

        return new AccessToken(
            accessToken: is_string($accessToken) ? $accessToken : '',
            scope: is_string($scope) ? $scope : '',
            expiresIn: is_int($expiresIn) ? $expiresIn : 0,
            tokenType: is_string($tokenType) ? $tokenType : '',
        );
    }
}
