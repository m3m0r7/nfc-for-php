<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC;

use NFC\Attributes\NFCTargetAttributeInterface;
use NFC\Contexts\ContextProxyInterface;
use NFC\Drivers\LibNFC\Attributes\Barcode;
use NFC\Drivers\LibNFC\Attributes\Dep;
use NFC\Drivers\LibNFC\Attributes\FeliCa;
use NFC\Drivers\LibNFC\Attributes\ISO14443A;
use NFC\Drivers\LibNFC\Attributes\ISO14443B;
use NFC\Drivers\LibNFC\Attributes\ISO14443B2CT;
use NFC\Drivers\LibNFC\Attributes\ISO14443B2SR;
use NFC\Drivers\LibNFC\Attributes\ISO14443BI;
use NFC\Drivers\LibNFC\Attributes\ISO14443BICLASS;
use NFC\Drivers\LibNFC\Attributes\Jewel;
use NFC\NFCContext;
use NFC\NFCDeviceInterface;
use NFC\NFCTargetException;
use NFC\NFCTargetInterface;

class NFCTarget implements NFCTargetInterface
{
    protected NFCContext $context;
    protected ContextProxyInterface $NFCTargetContext;
    protected NFCDeviceInterface $device;
    protected ?NFCTargetAttributeInterface $attribute = null;

    public function __construct(NFCContext $context, NFCDeviceInterface $device, ContextProxyInterface $NFCTargetContext)
    {
        $this->context = $context;
        $this->device = $device;
        $this->NFCTargetContext = $NFCTargetContext;

        $this->fillNFCTargetForPHPFFIBug();
    }

    protected function fillNFCTargetForPHPFFIBug(): void
    {
        // struct/union implementation are incomplete.
        // The PHP cannot overwrite structure instantiated on the PHP scope.
        if ($this->NFCTargetContext->nm->nmt !== 0) {
            return;
        }

        $modulationTypes = $this->context->getModulationsTypes();
        $baudRates = $this->context->getBaudRates();

        $text = (string) $this;
        if (!preg_match('/^(.+?)\((.+?)\)/', $text, $matches)) {
            throw new NFCTargetException('Cannot parse output from `str_nfc_target`');
        }

        [, $modulationTypeName, $baudRateOrModeName] = $matches;
        $modulationTypeName = trim($modulationTypeName);
        $baudRateOrModeName = trim($baudRateOrModeName);

        // Find from modulation types
        foreach ($modulationTypes->getValues() as $value) {
            if ($this->context->getFFI()->str_nfc_modulation_type($value) === $modulationTypeName) {
                $this->NFCTargetContext->nm->nmt = $value;
                break;
            }
        }

        if ($this->NFCTargetContext->nm->nmt !== $modulationTypes->NMT_DEP) {
            foreach ($baudRates->getValues() as $value) {
                if ($this->context->getFFI()->str_nfc_baud_rate($value) === $baudRateOrModeName) {
                    $this->NFCTargetContext->nm->nbr = $value;
                    break;
                }
            }
        }

        if ($this->NFCTargetContext->nm->nmt === 0 || $this->NFCTargetContext->nm->nbr === 0) {
            throw new NFCTargetException('Unknown NFC target type');
        }
    }

    public function getNFCContext(): NFCContext
    {
        return $this->context;
    }

    public function getNFCDevice(): NFCDeviceInterface
    {
        return $this->device;
    }

    public function __toString(): string
    {
        return $this
            ->context
            ->getOutput()
            ->outputNFCTargetContext($this);
    }

    public function getModulationType(): string
    {
        return $this
            ->context
            ->getFFI()
            ->str_nfc_modulation_type($this->NFCTargetContext->nm->nmt);
    }

    public function getBaudRate(): string
    {
        return $this
            ->context
            ->getFFI()
            ->str_nfc_baud_rate($this->NFCTargetContext->nm->nbr);
    }

    public function getNFCTargetContext(): ContextProxyInterface
    {
        return $this->NFCTargetContext;
    }

    public function getAttributeAccessor(): NFCTargetAttributeInterface
    {
        $modulationTypes = $this->context->getModulationsTypes();

        switch ($this->NFCTargetContext->nm->nmt) {
        case $modulationTypes->NMT_ISO14443A:
            return $this->attribute ??= new ISO14443A($this);
        case $modulationTypes->NMT_JEWEL:
            return $this->attribute ??= new Jewel($this);
        case $modulationTypes->NMT_ISO14443B:
            return $this->attribute ??= new ISO14443B($this);
        case $modulationTypes->NMT_ISO14443BI:
            return $this->attribute ??= new ISO14443BI($this);
        case $modulationTypes->NMT_ISO14443B2SR:
            return $this->attribute ??= new ISO14443B2SR($this);
        case $modulationTypes->NMT_ISO14443B2CT:
            return $this->attribute ??= new ISO14443B2CT($this);
        case $modulationTypes->NMT_FELICA:
            return $this->attribute ??= new FeliCa($this);
        case $modulationTypes->NMT_DEP:
            return $this->attribute ??= new Dep($this);
        case $modulationTypes->NMT_BARCODE:
            return $this->attribute ??= new Barcode($this);
        case $modulationTypes->NMT_ISO14443BICLASS:
            return $this->attribute ??= new ISO14443BICLASS($this);
        }

        throw new NFCTargetException("Unknown target [{$this->NFCTargetContext->nm->nmt}, {$this->NFCTargetContext->nm->nbr}]");
    }
}
