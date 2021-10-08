<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;
use NFC\NFCDebug;

$nfc = new NFC('/usr/local/Cellar/libnfc/1.8.0/lib/libnfc.dylib');
$context = $nfc->createContext();

$modulationTypes = $context->getModulationsTypes();
$baudRates = $context->getBaudRates();

$nfcDebug = new NFCDebug($context);

$context
    ->start(
        function (\NFC\NFCTargetContext $nfcTargetContext) {
            echo ((string) $nfcTargetContext) . "\n";
        },
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
