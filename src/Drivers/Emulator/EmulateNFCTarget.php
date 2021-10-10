<?php

declare(strict_types=1);

namespace NFC\Drivers\Emulator;

use NFC\Attributes\NFCTargetAttributeInterface;
use NFC\Contexts\ContextProxyInterface;
use NFC\NFCContext;
use NFC\NFCDeviceInterface;
use NFC\NFCTargetInterface;

class EmulateNFCTarget implements NFCTargetInterface
{
    protected NFCContext $context;
    protected NFCDeviceInterface $device;
    protected ContextProxyInterface $target;

    public function __construct(NFCContext $context, NFCDeviceInterface $device, ContextProxyInterface $NFCTargetContext)
    {
        $this->context = $context;
        $this->device = $device;
        $this->target = $NFCTargetContext;
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
        return 'emulated output';
    }

    public function getModulationType(): string
    {
        return 'FeliCa';
    }

    public function getBaudRate(): string
    {
        return '212 kbps';
    }

    public function getNFCTargetContext(): ContextProxyInterface
    {
        return $this->target;
    }

    public function getAttributeAccessor(): NFCTargetAttributeInterface
    {
        return new EmulateAttribute();
    }
}
