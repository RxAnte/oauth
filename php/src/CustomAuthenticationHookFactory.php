<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

readonly class CustomAuthenticationHookFactory
{
    public function __construct(
        private CustomAuthenticationHook|null $customAuthenticationHook = null,
    ) {
    }

    public function create(): CustomAuthenticationHook
    {
        return $this->customAuthenticationHook ?? new CustomAuthenticationHookNoOp();
    }
}
