<?php
declare(strict_types=1);

namespace NFC\Headers;

class HeaderEnum
{
    public static function all(): array
    {
        static $constants = null;
        return $constants ??= (new \ReflectionClass(static::class))
            ->getConstants();
    }
}
