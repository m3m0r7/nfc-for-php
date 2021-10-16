<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFCEventManager;

return (new NFCEventManager())
    ->listen(
        NFCEventManager::EVENT_OPEN,
        function (\NFC\NFCContext $context) {
            echo "Opened NFC Context.\n";
        }
    )
    ->listen(
        NFCEventManager::EVENT_CLOSE,
        function (\NFC\NFCContext $context) {
            echo "Closed NFC Context.\n";
        }
    )
    ->listen(
        NFCEventManager::EVENT_START,
        function (\NFC\NFCContext $context, \NFC\NFCDeviceInterface $device) {
            echo "NFC Reader started ({$context->getVersion()}): {$device->getDeviceName()}\n";
        }
    )
    ->listen(
        NFCEventManager::EVENT_TOUCH,
        function (\NFC\NFCContext $context, \NFC\NFCTargetInterface $NFCTargetContext) {
            echo "{$NFCTargetContext->getModulationType()} ({$NFCTargetContext->getBaudRate()}): {$NFCTargetContext->getAttributeAccessor()->getID()}\n";
        }
    )
    ->listen(
        NFCEventManager::EVENT_RELEASE,
        function (\NFC\NFCContext $context, \NFC\NFCTargetInterface $NFCTargetContext) {
            echo "Release: {$NFCTargetContext->getAttributeAccessor()->getID()}({$NFCTargetContext->getModulationType()})\n";
        }
    )
    ->listen(
        NFCEventManager::EVENT_ERROR,
        function (\NFC\NFCContext $context, Throwable $e) {
            echo "An error occurred:\n{$e}\n";
        }
    );