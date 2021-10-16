<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use NFC\Contexts\FFIContextProxy;
use NFC\Drivers\RCS380\Headers\LibUSBConstants;
use NFC\Util\SimpleKernel;

class Kernel extends SimpleKernel
{
    protected array $headers = [
        [__DIR__ . '/Headers/cdef/libusb.h', [LibUSBConstants::class]],
    ];

    protected array $autoScanLibraryNames = [
        'libusb.dylib',
        'libusb-1.0.dylib',
        'libusb.so',
        'libusb-1.0.so',
        'libusb.dll',
        'libusb-1.0.dll',
    ];

    protected string $driverClassName = RCS380Driver::class;
    protected string $FFIContextProxyClassName = FFIContextProxy::class;

    protected ?string $defaultLibraryPath = 'libusb/lib';
}
