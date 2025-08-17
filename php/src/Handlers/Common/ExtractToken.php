<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Common;

use Psr\Http\Message\ServerRequestInterface;

use function array_change_key_case;

use const CASE_LOWER;

/** @deprecated We're moving to the more generic andler in the RxAnte namespace and this class is not needed there */
readonly class ExtractToken
{
    public function fromAuthHeader(ServerRequestInterface $request): string
    {
        $headers = array_change_key_case(
            $request->getHeaders(),
            CASE_LOWER,
        );

        return $headers['authorization'][0] ?? '';
    }
}
