<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal;

use Firebase\JWT\Key;
use RxAnte\OAuth\Handlers\Auth0\Auth0Config;
use RxAnte\OAuth\UserInfo\Jwt;
use Throwable;

use function base64_decode;
use function explode;
use function implode;
use function is_array;
use function is_string;
use function json_decode;
use function str_replace;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class JwtFactory
{
    public function __construct(private Auth0Config $config)
    {
    }

    public function fromBearerToken(string $token): Jwt
    {
        $tokenString = explode(' ', $token)[1] ?? '';

        try {
            \Firebase\JWT\JWT::decode(
                $tokenString,
                new Key(
                    $this->config->signingCertificate,
                    $this->config->signingCertificateAlgorithm,
                ),
            );
        } catch (Throwable) {
            return new Jwt();
        }

        /** @var array<array-key, string|int|array<array-key, string>> $properties */
        $properties = json_decode(
            (string) base64_decode(
                str_replace(
                    '_',
                    '/',
                    str_replace(
                        '-',
                        '+',
                        explode('.', $token)[1] ?? '',
                    ),
                ),
                true,
            ),
            true,
        );

        $scope = $properties['scope'] ?? null;

        if (is_string($scope) && ! isset($properties['scopes'])) {
            $properties['scopes'] = explode(' ', $scope);
        }

        $scopes = ($properties['scopes'] ?? []);
        $scopes = is_array($scopes) ? $scopes : [];

        $aud = $properties['aud'] ?? '';

        if (is_array($aud)) {
            $aud = implode(' ', $aud);
        }

        return new Jwt(
            $token,
            (string) $aud,
            /** @phpstan-ignore-next-line */
            (string) ($properties['jti'] ?? ''),
            (int) ($properties['iat'] ?? ''),
            (int) ($properties['nbf'] ?? ''),
            (int) ($properties['exp'] ?? ''),
            /** @phpstan-ignore-next-line */
            (string) ($properties['sub'] ?? ''),
            $scopes,
        );
    }
}
