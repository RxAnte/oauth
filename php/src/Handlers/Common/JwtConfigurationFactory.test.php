<?php

declare(strict_types=1);

use Lcobucci\JWT\Configuration as JwtConfiguration;
use Mockery\MockInterface;
use Psr\Clock\ClockInterface;
use RxAnte\OAuth\Handlers\Common\JwtConfigurationFactory;
use RxAnte\OAuth\Handlers\Common\OauthPublicKey;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('JwtConfigurationFactory', function (): void {
    uses()->group('JwtConfigurationFactory');

    readonly class JwtConfigurationFactoryTestSetup
    {
        public JwtConfigurationFactory $sut;
        public MockInterface&ClockInterface $clock;
        public MockInterface&OauthPublicKey $publicKey;

        public function __construct()
        {
            $this->clock = Mockery::mock(ClockInterface::class);

            $this->publicKey = Mockery::mock(OauthPublicKey::class);

            $this->sut = new JwtConfigurationFactory(
                clock: $this->clock,
                publicKey: $this->publicKey,
            );
        }
    }

    it(
        'throws an exception if the public key is empty',
        function (): void {
            $setup = new JwtConfigurationFactoryTestSetup();

            $setup->publicKey->expects('getKeyContents')
                ->andReturn('');

            $setup->sut->create();
        },
    )->throws(
        RuntimeException::class,
        'Public key is empty',
    );

    it(
        'creates a JWT configuration when the public key is valid',
        function (
            string|null $passPhrase,
        ): void {
            $setup = new JwtConfigurationFactoryTestSetup();

            $setup->publicKey->expects('getKeyContents')
                ->andReturn('mock-key-contents');

            $setup->publicKey->expects('getPassPhrase')
                ->andReturn($passPhrase);

            $result = $setup->sut->create();

            expect($result)->toBeInstanceOf(
                JwtConfiguration::class,
            );
        },
        /** @phpstan-ignore-next-line */
    )->with([
        'with passphrase' => 'mock-passphrase',
        'without passphrase' => null,
    ]);
});
