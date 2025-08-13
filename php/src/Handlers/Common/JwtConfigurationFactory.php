<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Common;

use Lcobucci\JWT\Configuration as JwtConfiguration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Psr\Clock\ClockInterface;
use RuntimeException;

readonly class JwtConfigurationFactory
{
    public function __construct(
        private ClockInterface $clock,
        private OauthPublicKey $publicKey,
    ) {
    }

    public function create(): JwtConfiguration
    {
        $jwtConfiguration = JwtConfiguration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('empty', 'empty'),
        );

        $publicKeyContents = $this->publicKey->getKeyContents();

        if ($publicKeyContents === '') {
            throw new RuntimeException('Public key is empty');
        }

        return $jwtConfiguration->withValidationConstraints(
            new LooseValidAt($this->clock),
            new SignedWith(
                new Sha256(),
                InMemory::plainText(
                    $publicKeyContents,
                    $this->publicKey->getPassPhrase() ?? '',
                ),
            ),
        );
    }
}
