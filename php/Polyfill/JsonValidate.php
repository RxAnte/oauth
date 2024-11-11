<?php

declare(strict_types=1);

defined('JSON_MAX_DEPTH') || define('JSON_MAX_DEPTH', 0x7FFFFFFF);

if (! function_exists('json_validate')) {
    function json_validate(string $json, int $depth = 512, int $flags = 0): bool
    {
        if ($flags !== 0 && defined('JSON_INVALID_UTF8_IGNORE') && $flags !== JSON_INVALID_UTF8_IGNORE) {
            throw new ValueError('json_validate(): Argument #3 ($flags) must be a valid flag (allowed flags: JSON_INVALID_UTF8_IGNORE)');
        }

        if ($depth <= 0) {
            throw new ValueError('json_validate(): Argument #2 ($depth) must be greater than 0');
        }

        if ($depth > JSON_MAX_DEPTH) {
            throw new ValueError(sprintf('json_validate(): Argument #2 ($depth) must be less than %d', JSON_MAX_DEPTH));
        }

        json_decode($json, null, $depth, $flags);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
