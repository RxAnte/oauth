<?php

declare(strict_types=1);

namespace RxAnte\OAuth\UserInfo;

use function md5;

readonly class Jwt
{
    public string $cacheKey;

    /**
     * @see https://auth0.com/docs/secure/tokens/json-web-tokens/json-web-token-claims
     *
     * @param string   $rawToken the original raw token
     * @param string   $aud      (audience): Recipient for which the JWT is intended. its value contains the ID of either an application (Client ID) for an ID Token or an API (API Identifier) for an Access Token.
     * @param string   $jti      (JWT ID): Unique identifier; can be used to prevent the JWT from being replayed (allows a token to be used only once)
     * @param int      $iat      (issued at time): Time at which the JWT was issued; can be used to determine age of the JWT
     * @param int      $nbf      (not before time): Time before which the JWT must not be accepted for processing
     * @param int      $exp      (expiration time): Time after which the JWT expires
     * @param string   $sub      (subject): Subject of the JWT (the user)
     * @param string[] $scopes
     */
    public function __construct(
        public string $rawToken,
        public string $aud,
        public string $jti,
        public int $iat,
        public int $nbf,
        public int $exp,
        public string $sub,
        public array $scopes,
    ) {
        $this->cacheKey = 'user_info_token_' . md5($rawToken);
    }
}
