<?php

declare(strict_types=1);

namespace NFC\Drivers\Emulator;

use NFC\Collections\NFCModulations;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NullContextProxy;
use NFC\Drivers\DriverInterface;
use NFC\NFCBaudRatesInterface;
use NFC\NFCContext;
use NFC\NFCDeviceInterface;
use NFC\NFCModulationTypesInterface;
use NFC\NFCTargetInterface;

class EmulateDriver implements DriverInterface
{
    protected NFCContext $context;

    public function __construct(NFCContext $context)
    {
        $this->context = $context;
    }

    public function open(): DriverInterface
    {
        return $this;
    }

    public function close(): void
    {
    }

    public function getVersion(): string
    {
        return '0.0.1';
    }

    public function getDevices(bool $includeCannotOpenDevices = false): array
    {
        return [
            (new EmulateNFCDevice())->setDeviceIndex(1),
            (new EmulateNFCDevice())->setDeviceIndex(2),
            (new EmulateNFCDevice())->setDeviceIndex(3),
        ];
    }

    public function findDeviceNameContain(string $deviceName): NFCDeviceInterface
    {
        return new EmulateNFCDevice();
    }

    public function start(NFCDeviceInterface $device = null, NFCModulations $modulations = null): void
    {
        $this->context
            ->getNFC()
            ->getLogger()
            ->info('started');
    }

    public function isPresent(NFCDeviceInterface $device, NFCTargetInterface $target): bool
    {
        return true;
    }

    public function getBaudRates(): NFCBaudRatesInterface
    {
        return new EmulateNFCBaudRates();
    }

    public function getModulationsTypes(): NFCModulationTypesInterface
    {
        return new EmulateNFCModulationTypes();
    }

    public function getNFCContext(): ContextProxyInterface
    {
        return new NullContextProxy();
    }
}
