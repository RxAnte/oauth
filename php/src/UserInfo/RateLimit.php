<?php

declare(strict_types=1);

namespace RxAnte\OAuth\UserInfo;

use Exception;

class RateLimit extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'The UserInfo request has been rate-limited',
            429,
        );
    }
}
