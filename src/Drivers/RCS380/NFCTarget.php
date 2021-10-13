<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use NFC\Attributes\NFCTargetAttributeInterface;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NullContextProxy;
use NFC\Drivers\RCS380\Attributes\FeliCa;
use NFC\NFCContext;
use NFC\NFCDeviceInterface;
use NFC\NFCTargetInterface;

class NFCTarget implements NFCTargetInterface
{
    protected NFCContext $context;
    protected NFCDeviceInterface $device;
    protected ?NFCTargetAttributeInterface $attribute = null;
    protected string $packet;

    public function __construct(NFCContext $context, NFCDeviceInterface $device, string $packet)
    {
        $this->context = $context;
        $this->device = $device;
        $this->packet = $packet;
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
        // FIXME
        return 'FeliCa';
    }

    public function getBaudRate(): string
    {
        // FIXME
        return '212 kbps';
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
}