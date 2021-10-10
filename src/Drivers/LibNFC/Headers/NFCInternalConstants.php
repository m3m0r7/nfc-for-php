<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Headers;

class NFCInternalConstants extends ConstantsEnum
{
    public const DEVICE_NAME_LENGTH = 256;
    public const DEVICE_PORT_LENGTH = 64;
    public const MAX_USER_DEFINED_DEVICES = 4;
}
