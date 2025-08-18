<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use League\OAuth2\Client\Provider\GenericProvider;

class RxAnteOauthProvider extends GenericProvider
{
    protected string|null $audience = null;

    /** @return string[] */
    protected function getConfigurableOptions(): array
    {
        $options = parent::getConfigurableOptions();

        $options[] = 'audience';

        return $options;
    }

    /**
     * @param mixed[] $options
     *
     * @return string[]
     */
    protected function getAuthorizationParameters(array $options): array
    {
        $params = parent::getAuthorizationParameters($options);

        if ($this->audience !== null) {
            $params['audience'] = $this->audience;
        }

        return $params;
    }
}
