<?php

declare(strict_types=1);

namespace NFC\Drivers;

use FFI\CData;
use NFC\Collections\NFCModulations;
use NFC\NFCBaudRates;
use NFC\NFCContext;
use NFC\NFCDeviceInterface;
use NFC\NFCModulationTypes;
use NFC\NFCTargetInterface;

interface DriverInterface
{
    public function __construct(NFCContext $context);
    public function open(): self;
    public function close(): void;
    public function getVersion(): string;
    public function getDevices(bool $includeCannotOpenDevices = false): array;
    public function findDeviceNameContain(string $deviceName): NFCDeviceInterface;
    public function start(NFCDeviceInterface $device = null, NFCModulations $modulations = null);
    public function isPresent(NFCDeviceInterface $device, NFCTargetInterface $target): bool;
    public function getBaudRates(): NFCBaudRates;
    public function getModulationsTypes(): NFCModulationTypes;
    public function getNFCContext(): CData;
}
