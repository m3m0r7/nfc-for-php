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
        [
            new \NFC\NFCModulation($modulationTypes->NMT_ISO14443A, $baudRates->NBR_106),
            new \NFC\NFCModulation($modulationTypes->NMT_ISO14443B, $baudRates->NBR_106),
            new \NFC\NFCModulation($modulationTypes->NMT_FELICA, $baudRates->NBR_212),
            new \NFC\NFCModulation($modulationTypes->NMT_FELICA, $baudRates->NBR_424),
            new \NFC\NFCModulation($modulationTypes->NMT_JEWEL, $baudRates->NBR_106),
            new \NFC\NFCModulation($modulationTypes->NMT_ISO14443BICLASS, $baudRates->NBR_106),
        ],
        $context->findDeviceNameContain('Sony'),
    );
