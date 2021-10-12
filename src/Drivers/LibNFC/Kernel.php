<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC;

use NFC\Contexts\FFIContextProxy;
use NFC\Drivers\LibNFC\Headers\NFCConstants;
use NFC\Drivers\LibNFC\Headers\NFCInternalConstants;
use NFC\Drivers\LibNFC\Headers\NFCTypesConstants;
use NFC\NFCInterface;
use NFC\Util\SimpleKernel;

class Kernel extends SimpleKernel implements NFCInterface
{
    protected array $headers = [
        [__DIR__ . '/Headers/cdef/nfc-types.h', [NFCTypesConstants::class]],
        [__DIR__ . '/Headers/cdef/nfc-internal.h', [NFCInternalConstants::class]],
        [__DIR__ . '/Headers/cdef/nfc.h', [NFCConstants::class]],
    ];

    protected array $autoScanLibraryNames = [
        'libnfc.dylib',
        'libnfc.6.dylib',
        'libnfc.so',
        'libnfc.6.so',
        'libnfc.dll',
        'libnfc.6.dll',
    ];

    protected string $driverClassName = LibNFCDriver::class;
    protected string $FFIContextProxyClassName = FFIContextProxy::class;

    protected ?string $defaultLibraryPath = 'libnfc/lib';
}
