<?php
declare(strict_types=1);

namespace NFC;

use FFI\CData;
use NFC\Attributes\Barcode;
use NFC\Attributes\Dep;
use NFC\Attributes\Felica;
use NFC\Attributes\ISO14443A;
use NFC\Attributes\ISO14443B;
use NFC\Attributes\ISO14443B2CT;
use NFC\Attributes\ISO14443B2SR;
use NFC\Attributes\ISO14443BI;
use NFC\Attributes\ISO14443BICLASS;
use NFC\Attributes\Jewel;
use NFC\Attributes\NFCTargetAttributeInterface;
use Throwable;

class NFCTarget
{
    protected NFCContext $context;
    protected CData $nfcTargetContext;
    protected NFCDevice $device;
    protected ?NFCTargetAttributeInterface $attribute = null;

    public function __construct(NFCContext $context, NFCDevice $device, CData $nfcTargetContext)
    {
        $this->context = $context;
        $this->device = $device;
        $this->nfcTargetContext = $nfcTargetContext;

        $this->fillNFCTargetForPHPFFIBug();
    }

    protected function fillNFCTargetForPHPFFIBug(): void
    {
        // struct/union implementation are incomplete.
        // The PHP cannot overwrite structure instantiated on the PHP scope.
        if ($this->nfcTargetContext->nm->nmt !== 0) {
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
        foreach ($modulationTypes->getEnums() as $value) {
            if ($this->context->getFFI()->str_nfc_modulation_type($value) === $modulationTypeName) {
                $this->nfcTargetContext->nm->nmt = $value;
                break;
            }
        }

        if ($this->nfcTargetContext->nm->nmt !== $modulationTypes->NMT_DEP) {
            foreach ($baudRates->getEnums() as $value) {
                if ($this->context->getFFI()->str_nfc_baud_rate($value) === $baudRateOrModeName) {
                    $this->nfcTargetContext->nm->nbr = $value;
                    break;
                }
            }
        }

        if ($this->nfcTargetContext->nm->nmt === 0 || $this->nfcTargetContext->nm->nbr === 0) {
            throw new NFCTargetException('Unknown NFC target type');
        }
    }

    public function getNFCContext(): NFCContext
    {
        return $this->context;
    }

    public function getNFCDevice(): NFCDevice
    {
        return $this->device;
    }

    public function __toString(): string
    {
        return $this
            ->context
            ->getOutput()
            ->outputNFCTargetContext(
                $this->nfcTargetContext
            );
    }

    public function getTargetName(): string
    {
        return $this
            ->context
            ->getFFI()
            ->str_nfc_modulation_type($this->nfcTargetContext->nm->nmt);
    }

    public function getBaudRate(): string
    {
        return $this
            ->context
            ->getFFI()
            ->str_nfc_baud_rate($this->nfcTargetContext->nm->nbr);
    }

    public function getNFCTargetContext(): CData
    {
        return $this->nfcTargetContext;
    }

    public function getAttributeAccessor(): NFCTargetAttributeInterface
    {
        $modulationTypes = $this->context->getModulationsTypes();

        switch ($this->nfcTargetContext->nm->nmt) {
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
                return $this->attribute ??= new Felica($this);
            case $modulationTypes->NMT_DEP:
                return $this->attribute ??= new Dep($this);
            case $modulationTypes->NMT_BARCODE:
                return $this->attribute ??= new Barcode($this);
            case $modulationTypes->NMT_ISO14443BICLASS:
                return $this->attribute ??= new ISO14443BICLASS($this);
        }

        throw new NFCTargetException("Unknown target [{$this->nfcTargetContext->nm->nmt}, {$this->nfcTargetContext->nm->nbr}]");
    }
}