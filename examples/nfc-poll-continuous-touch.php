<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;
use NFC\NFCEventManager;

$nfc = new NFC('/usr/local/Cellar/libnfc/1.8.0/lib/libnfc.dylib');

$context = $nfc->createContext(
    (new \NFC\NFCEventManager())
        ->listen(
            NFCEventManager::EVENT_TOUCH,
            function (\NFC\NFCContext $context, \NFC\NFCTarget $nfcTargetContext) {
                echo ((string) $nfcTargetContext) . "\n";
            }
        )
);

$modulationTypes = $context->getModulationsTypes();
$baudRates = $context->getBaudRates();

$context
    ->enableContinuousTouchAdjustment(false)
    ->start(
        $context->findDeviceNameContain('Sony'),
        (new \NFC\Util\PredefinedModulations($context))
            ->all(),
    );
