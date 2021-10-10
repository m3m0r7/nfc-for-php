<?php
declare(strict_types=1);

namespace NFC\Headers;

class ConstantsEnum
{
    public static function all(): array
    {
        static $constants = null;
        return $constants ??= (new \ReflectionClass(static::class))
            ->getConstants();
    }
}
