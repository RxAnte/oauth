<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal;

use Firebase\JWT\Key;
use RxAnte\OAuth\Handlers\FusionAuth\FusionAuthConfig;
use RxAnte\OAuth\UserInfo\Jwt;
use Throwable;

use function explode;
use function implode;
use function is_array;
use function is_string;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class JwtFactory
{
    public function __construct(private FusionAuthConfig $config)
    {
    }

    public function fromBearerToken(string $token): Jwt
    {
        $tokenString = explode(' ', $token)[1] ?? '';

        try {
            $fbToken = \Firebase\JWT\JWT::decode(
                $tokenString,
                new Key(
                    $this->config->signingCertificate,
                    $this->config->signingCertificateAlgorithm,
                ),
            );
        } catch (Throwable) {
            return new Jwt();
        }

        $aud = $fbToken->aud ?? '';

        if (is_array($aud)) {
            $aud = implode(' ', $aud);
        }

        $scope = $fbToken->scope ?? null;

        if (is_string($scope) && ! isset($fbToken->scopes)) {
            $fbToken->scopes = explode(' ', $scope);
        }

        $scopes = ($fbToken->scopes ?? []);
        $scopes = is_array($scopes) ? $scopes : [];

        return new Jwt(
            $token,
            (string) $aud,
            (string) ($fbToken->jti ?? ''),
            (int) ($fbToken->iat ?? ''),
            0,
            (int) ($fbToken->exp ?? ''),
            (string) ($fbToken->sub ?? ''),
            $scopes,
        );
    }
}
