<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;

$NFC = new NFC(
    \NFC\Drivers\RCS380\Kernel::class,
    \NFC\Util\OS::isMac()
        ? '/usr/local/Cellar/libusb/1.0.24/lib/libusb-1.0.dylib'
        : '/usr/local/lib/libusb-1.0.so',
);
$context = $NFC->createContext();

/**
 * @var \NFC\NFCDeviceInfo $device
 */
foreach ($context->getDevices(true) as $device) {
    echo "{$device->getDeviceName()} [{$device->getConnectionTarget()}]\n";
}
