<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Headers;

class NFCLogConstants extends ConstantsEnum
{
    public const NFC_LOG_PRIORITY_NONE  = 0;
    public const NFC_LOG_PRIORITY_ERROR = 1;
    public const NFC_LOG_PRIORITY_INFO  = 2;
    public const NFC_LOG_PRIORITY_DEBUG = 3;

    public const NFC_LOG_GROUP_GENERAL  = 1;
    public const NFC_LOG_GROUP_CONFIG   = 2;
    public const NFC_LOG_GROUP_CHIP     = 3;
    public const NFC_LOG_GROUP_DRIVER   = 4;
    public const NFC_LOG_GROUP_COM      = 5;
    public const NFC_LOG_GROUP_LIBUSB   = 6;
}
