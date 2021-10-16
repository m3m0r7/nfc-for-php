<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;
use NFC\NFCEventManager;

$NFC = new NFC(
    \NFC\Drivers\RCS380\Kernel::class,
    \NFC\Util\OS::isMac()
        ? '/usr/local/Cellar/libusb/1.0.24/lib/libusb-1.0.dylib'
        : '/usr/local/lib/libusb-1.0.so',
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
    ->start($context->findDeviceName('RC-S380/P'));