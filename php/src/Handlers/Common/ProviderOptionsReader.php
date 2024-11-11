<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Common;

use League\OAuth2\Client\Provider\AbstractProvider as OauthClientProvider;

use function chr;
use function is_string;

// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

readonly class ProviderOptionsReader
{
    /** @phpstan-ignore-next-line */
    private array $options;

    private string $prefix;

    public function __construct(OauthClientProvider $provider)
    {
        $this->options = (array) $provider;

        $this->prefix = chr(0) . '*' . chr(0);
    }

    public function clientId(): string
    {
        return $this->getValue('clientId');
    }

    public function clientSecret(): string
    {
        return $this->getValue('clientSecret');
    }

    private function getValue(string $key): string
    {
        $val = $this->options[$this->prefix . $key] ?? '';

        if (! is_string($val)) {
            return '';
        }

        return $val;
    }
}
