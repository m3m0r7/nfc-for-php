<?php

declare(strict_types=1);

namespace NFC\Drivers;

use NFC\Collections\NFCModulationsInterface;
use NFC\Contexts\ContextProxyInterface;
use NFC\NFCBaudRatesInterface;
use NFC\NFCContext;
use NFC\NFCDeviceInterface;
use NFC\NFCModulationTypesInterface;
use NFC\NFCTargetInterface;
use NFC\Util\PredefinedModulations;

interface DriverInterface
{
    public function __construct(NFCContext $context);
    public function open(): self;
    public function close(): void;
    public function getVersion(): string;
    public function getDevices(bool $includeCannotOpenDevices = false): array;
    public function findDeviceName(string $deviceName): NFCDeviceInterface;
    public function start(NFCDeviceInterface $device = null, NFCModulationsInterface $modulations = null): void;
    public function isPresent(NFCDeviceInterface $device, NFCTargetInterface $target): bool;
    public function getBaudRates(): NFCBaudRatesInterface;
    public function getModulationsTypes(): NFCModulationTypesInterface;
    public function getNFCContext(): ContextProxyInterface;
}
