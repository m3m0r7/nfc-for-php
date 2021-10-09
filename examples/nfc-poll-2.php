<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;

$nfc = new NFC('/usr/local/Cellar/libnfc/1.8.0/lib/libnfc.dylib');

$context = $nfc->createContext(
    (new \NFC\NFCEventManager())
        ->addEventListener(
            'open',
            function (\NFC\NFCContext $context) {
                echo "Opened NFC Context.\n";
            }
        )
        ->addEventListener(
            'close',
            function (\NFC\NFCContext $context) {
                echo "Closed NFC Context.\n";
            }
        )
        ->addEventListener(
            'start',
            function (\NFC\NFCContext $context, \NFC\NFCDevice $device) {
                echo "NFC Reader started ({$context->getVersion()}): {$device->getDeviceName()}\n";
            }
        )
        ->addEventListener(
            'touch',
            function (\NFC\NFCContext $context, \NFC\NFCTarget $nfcTargetContext) {
                echo "{$nfcTargetContext->getTargetName()}({$nfcTargetContext->getBaudRate()})): {$nfcTargetContext->getAttributeAccessor()->getID()}\n";
            }
        )
        ->addEventListener(
            'leave',
            function (\NFC\NFCContext $context, \NFC\NFCTarget $nfcTargetContext) {
                echo "Leave: {$nfcTargetContext->getAttributeAccessor()->getID()}({$nfcTargetContext->getTargetName()})\n";
            }
        )
        ->addEventListener(
            'error',
            function (\NFC\NFCContext $context, Throwable $e) {
                echo "An error occurred:\n{$e}\n";
            }
        )
);
$modulationTypes = $context->getModulationsTypes();
$baudRates = $context->getBaudRates();

$context
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
