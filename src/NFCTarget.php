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
    protected NFCDebug $debug;
    protected NFCDevice $device;

    public function __construct(NFCContext $context, NFCDevice $device, CData $nfcTargetContext)
    {
        $this->context = $context;
        $this->device = $device;
        $this->nfcTargetContext = $nfcTargetContext;

        $this->debug = new NFCDebug($this->context);
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
            ->debug
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

    public function getAttributeAccessor(): NFCTargetAttributeInterface
    {
        $modulationTypes = $this->context->getModulationsTypes();

        switch ($this->nfcTargetContext->nm->nmt) {
            case $modulationTypes->NMT_ISO14443A:
                return new ISO14443A($this);
            case $modulationTypes->NMT_JEWEL:
                return new Jewel($this);
            case $modulationTypes->NMT_ISO14443B:
                return new ISO14443B($this);
            case $modulationTypes->NMT_ISO14443BI:
                return new ISO14443BI($this);
            case $modulationTypes->NMT_ISO14443B2SR:
                return new ISO14443B2SR($this);
            case $modulationTypes->NMT_ISO14443B2CT:
                return new ISO14443B2CT($this);
            case $modulationTypes->NMT_FELICA:
                return new Felica($this);
            case $modulationTypes->NMT_DEP:
                return new Dep($this);
            case $modulationTypes->NMT_BARCODE:
                return new Barcode($this);
            case $modulationTypes->NMT_ISO14443BICLASS:
                return new ISO14443BICLASS($this);
        }

        throw new NFCTargetException('Unknown target');
    }
}