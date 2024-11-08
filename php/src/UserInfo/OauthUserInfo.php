<?php

declare(strict_types=1);

namespace RxAnte\OAuth\UserInfo;

readonly class OauthUserInfo
{
    public function __construct(
        public bool $isValid = false,
        public string $sub = '',
        public string $email = '',
        public string $name = '',
        public string $givenName = '',
        public string $familyName = '',
        public string $picture = '',
    ) {
    }
}
