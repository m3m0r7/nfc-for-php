<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use NFC\Attributes\NFCTargetAttributeInterface;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NullContextProxy;
use NFC\Drivers\RCS380\Attributes\FeliCa;
use NFC\NFCBaudRatesInterface;
use NFC\NFCContext;
use NFC\NFCDeviceInterface;
use NFC\NFCModulation;
use NFC\NFCModulationTypesInterface;
use NFC\NFCTargetInterface;

class NFCTarget implements NFCTargetInterface
{
    protected NFCContext $context;
    protected NFCDeviceInterface $device;
    protected ?NFCTargetAttributeInterface $attribute = null;
    protected string $packet;
    protected NFCModulation $modulations;
    protected NFCModulationTypesInterface $modulationsTypes;
    protected NFCBaudRatesInterface $baudRates;

    public function __construct(NFCModulation $modulations, NFCContext $context, NFCDeviceInterface $device, string $packet)
    {
        $this->modulations = $modulations;
        $this->context = $context;
        $this->device = $device;
        $this->packet = $packet;

        $this->modulationsTypes = new NFCModulationTypes($context->getFFI());
        $this->baudRates = new NFCBaudRates($context->getFFI());
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
        // FIXME
        return <<< _
        {$this->getModulationType()} ({$this->getBaudRate()})
            IDm: {$this->getAttributeAccessor()->getID()}
        _;
    }

    public function getModulationType(): string
    {
        switch ($this->modulations->getModulationType()) {
        case $this->modulationsTypes->NMT_FELICA:
            return 'FeliCa';
        }

        return 'Unknown';
    }

    public function getBaudRate(): string
    {
        switch ($this->modulations->getBaudRate()) {
        case $this->baudRates->NBR_106:
            return '106 kbps';
        case $this->baudRates->NBR_212:
            return '212 kbps';
        case $this->baudRates->NBR_424:
            return '424 kbps';
        case $this->baudRates->NBR_847:
            return '847 kbps';
        }

        return 'Unknown';
    }

    public function getNFCTargetContext(): ContextProxyInterface
    {
        return new NullContextProxy();
    }

    public function getAttributeAccessor(): NFCTargetAttributeInterface
    {
        return $this->attribute ??= new FeliCa($this);
    }

    public function getPacket(): string
    {
        return $this->packet;
    }

    public function getNFCModulation(): NFCModulation
    {
        return $this->modulations;
    }
}