<?php
declare(strict_types=1);

namespace NFC;

class NFCMacroHeaders
{
    public const DEVICE_NAME_LENGTH = 256;
    public const DEVICE_PORT_LENGTH = 64;
    public const MAX_USER_DEFINED_DEVICES = 4;
    public const NFC_BUFSIZE_CONNSTRING = 1024;

    public static function all(): array
    {
        return (new \ReflectionClass(static::class))
            ->getConstants();
    }
}