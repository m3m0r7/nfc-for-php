<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;
use NFC\NFCEventManager;

$NFC = new NFC(
    \NFC\Drivers\LibNFC\Kernel::class,
    '/usr/local/Cellar/libnfc/1.8.0/lib/libnfc.dylib'
);

$context = $NFC->createContext(require __DIR__ . '/event-manager.php');

$context
    ->start(
        $context->findDeviceName('Sony'),
    );
