<?php

declare(strict_types=1);

namespace NFC\Util;

class OS
{
    public static function isWindows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }

    public static function isMac(): bool
    {
        return PHP_OS === 'Darwin';
    }
}