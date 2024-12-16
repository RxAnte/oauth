<?php

declare(strict_types=1);

namespace RxAnte\OAuth\UserInfo;

readonly class OauthUserInfo
{
    /** @param string[] $roles */
    public function __construct(
        public bool $isValid = false,
        public string $sub = '',
        public string $email = '',
        public string $name = '',
        public string $givenName = '',
        public string $familyName = '',
        public string $picture = '',
        public array $roles = [],
    ) {
    }

    public function hasRole(string $role): bool
    {
        foreach ($this->roles as $existingRole) {
            if ($existingRole !== $role) {
                continue;
            }

            return true;
        }

        return false;
    }
}
