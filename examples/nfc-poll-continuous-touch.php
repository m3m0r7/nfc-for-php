<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;
use NFC\NFCEventManager;

$NFC = new NFC(
    \NFC\Drivers\LibNFC\Kernel::class,
    '/usr/local/Cellar/libnfc/1.8.0/lib/libnfc.dylib'
);

$context = $NFC->createContext(
    (new \NFC\NFCEventManager())
        ->listen(
            NFCEventManager::EVENT_TOUCH,
            function (\NFC\NFCContext $context, \NFC\NFCTargetInterface $NFCTargetContext) {
                echo ((string) $NFCTargetContext) . "\n";
            }
        )
);

$context
    ->getDriver()
    ->enableContinuousTouchAdjustment(false);

$context
    ->start(
        $context->findDeviceName('Sony'),
    );
