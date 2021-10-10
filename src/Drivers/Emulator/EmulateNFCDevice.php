<?php

declare(strict_types=1);

namespace NFC\Drivers\Emulator;

use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NullContextProxy;
use NFC\NFCDeviceInterface;

class EmulateNFCDevice implements NFCDeviceInterface
{
    protected int $index = 1;

    public function setDeviceIndex(int $index): self
    {
        $this->index = max($index, 1);
        return $this;
    }

    public function open(?string $connection = null, bool $forceOpen = false): NFCDeviceInterface
    {
        return $this;
    }

    public function getDeviceName(): string
    {
        return "emulator-{$this->index}";
    }

    public function isOpened(): bool
    {
        return true;
    }

    public function getLastErrorCode(): int
    {
        return 0;
    }

    public function getLastErrorName(): string
    {
        return 'None';
    }

    public function getConnection(): string
    {
        return 'emulator';
    }

    public function getDeviceContext(): ContextProxyInterface
    {
        return new NullContextProxy();
    }

    public function close(): void
    {
    }
}
