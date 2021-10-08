<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;

$nfc = new NFC('/usr/local/Cellar/libnfc/1.8.0/lib/libnfc.dylib');
$context = $nfc->createContext();

/**
 * @var \NFC\NFCDeviceInfo $device
 */
foreach ($context->getDevices() as $device) {
    echo "{$device->getDeviceName()} [{$device->getConnectionTarget()}]\n";
}
