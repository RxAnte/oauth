<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use Psr\Http\Message\ResponseInterface;

interface CustomResponseFactory
{
    public function create(): ResponseInterface;
}
