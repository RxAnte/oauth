<?php

declare(strict_types=1);

use League\OAuth2\Client\Provider\GenericProvider;
use RxAnte\OAuth\Handlers\Common\ProviderOptionsReader;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('ProviderOptionsReader', function (): void {
    uses()->group('ProviderOptionsReader');

    readonly class ProviderOptionsReaderTestSetup
    {
        public ProviderOptionsReader $sut;

        public function __construct(
            string|null $clientId,
            string|int|null $clientSecret,
        ) {
            $this->sut = new ProviderOptionsReader(
                new GenericProvider([
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'urlAuthorize' => 'https://auth',
                    'urlAccessToken' => 'https://token',
                    'urlResourceOwnerDetails' => 'https://deets',
                ]),
            );
        }
    }

    it('returns the client id', function (): void {
        $setup = new ProviderOptionsReaderTestSetup(
            clientId: 'mock-client-id',
            clientSecret: 'mock-client-secret',
        );

        $result = $setup->sut->clientId();

        expect($result)->toBe('mock-client-id');
    });

    it('returns the client secret', function (): void {
        $setup = new ProviderOptionsReaderTestSetup(
            clientId: 'mock-client-id',
            clientSecret: 'mock-client-secret',
        );

        $result = $setup->sut->clientSecret();

        expect($result)->toBe('mock-client-secret');
    });

    it(
        'returns an empty string if client id is not set',
        function (): void {
            $setup = new ProviderOptionsReaderTestSetup(
                clientId: null,
                clientSecret: 'mock-client-secret',
            );

            $result = $setup->sut->clientId();

            expect($result)->toBe('');
        },
    );

    it(
        'returns an empty string if client secret is not set',
        function (): void {
            $setup = new ProviderOptionsReaderTestSetup(
                clientId: 'mock-client-id',
                clientSecret: 123,
            );

            $result = $setup->sut->clientSecret();

            expect($result)->toBe('');
        },
    );
});
