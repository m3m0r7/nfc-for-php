<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;

$NFC = new NFC(
    \NFC\Drivers\LibNFC\Kernel::class,
    '/usr/local/Cellar/libnfc/1.8.0/lib/libnfc.dylib'
);
$context = $NFC->createContext();

/**
 * @var \NFC\NFCDeviceInfo $device
 */
foreach ($context->getDevices(true) as $device) {
    echo "{$device->getDeviceName()} [{$device->getConnectionTarget()}]\n";
}
