<?php
declare(strict_types=1);

namespace NFC\Drivers;

use FFI\CData;
use NFC\Collections\NFCModulations;
use NFC\NFCBaudRates;
use NFC\NFCContext;
use NFC\NFCDevice;
use NFC\NFCModulationTypes;
use NFC\NFCTarget;

interface DriverInterface
{
    public function __construct(NFCContext $context);
    public function open(): self;
    public function close(): void;
    public function getVersion(): string;
    public function getDevices(bool $includeCannotOpenDevices = false);
    public function findDeviceNameContain(string $deviceName);
    public function start(NFCDevice $device = null, NFCModulations $modulations = null);
    public function isPresent(NFCDevice $device, NFCTarget $target): bool;
    public function getBaudRates(): NFCBaudRates;
    public function getModulationsTypes(): NFCModulationTypes;
    public function getNFCContext(): CData;
}
