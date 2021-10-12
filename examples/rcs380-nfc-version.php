<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;

$NFC = new NFC(
    \NFC\Drivers\RCS380\Kernel::class,
    '/usr/local/Cellar/libusb/1.0.24/lib/libusb-1.0.dylib'
);

$context = $NFC->createContext();

echo "NFC Version: {$context->getVersion()}\n";
