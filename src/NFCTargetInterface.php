<?php

declare(strict_types=1);

namespace NFC;

use NFC\Attributes\NFCTargetAttributeInterface;
use NFC\Contexts\ContextProxyInterface;

interface NFCTargetInterface
{
    public function getNFCContext(): NFCContext;
    public function getNFCDevice(): NFCDeviceInterface;
    public function __toString(): string;
    public function getModulationType(): string;
    public function getBaudRate(): string;
    public function getNFCTargetContext(): ContextProxyInterface;
    public function getAttributeAccessor(): NFCTargetAttributeInterface;
}
