<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\Handlers\Common\JwtConfigurationFactory;
use Throwable;

use function assert;

readonly class JwtFactory
{
    public function __construct(
        private JwtConfigurationFactory $jwtConfigurationFactory,
    ) {
    }

    public function createFromToken(string $token): JwtToken
    {
        if ($token === '') {
            return new EmptyJwt();
        }

        $jwtConfiguration = $this->jwtConfigurationFactory->create();

        try {
            $jwt = $jwtConfiguration->parser()->parse($token);

            assert($jwt instanceof JwtToken);

            $jwtConfiguration->validator()->assert(
                $jwt,
                ...$jwtConfiguration->validationConstraints(),
            );

            return $jwt;
        } catch (Throwable) {
            return new EmptyJwt();
        }
    }
}
