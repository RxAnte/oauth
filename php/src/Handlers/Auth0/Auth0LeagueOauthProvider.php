<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0;

use League\OAuth2\Client\Provider\GenericProvider;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
class Auth0LeagueOauthProvider extends GenericProvider
{
    protected string $audience;

    /** @return string[] */
    protected function getConfigurableOptions(): array
    {
        $options = parent::getConfigurableOptions();

        $options[] = 'audience';

        return $options;
    }

    /** @return string[] */
    protected function getRequiredOptions(): array
    {
        $options = parent::getRequiredOptions();

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

        $params['audience'] = $this->audience;

        return $params;
    }
}
