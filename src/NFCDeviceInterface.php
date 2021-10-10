<?php

declare(strict_types=1);

namespace NFC;

use NFC\Contexts\ContextProxyInterface;

interface NFCDeviceInterface
{
    public function open(?string $connection = null, bool $forceOpen = false): self;
    public function getDeviceName(): string;
    public function isOpened(): bool;
    public function getLastErrorCode(): int;
    public function getLastErrorName(): string;
    public function getConnection(): string;
    public function getDeviceContext(): ContextProxyInterface;
    public function close(): void;
}
