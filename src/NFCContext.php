<?php
declare(strict_types=1);

namespace NFC;

use FFI\CData;
use NFC\Collections\NFCModulations;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\FFIContextProxy;
use NFC\Drivers\DriverInterface;

class NFCContext
{
    protected NFCInterface $nfc;
    protected FFIContextProxy $ffi;
    protected NFCOutput $output;
    protected DriverInterface $driver;
    protected NFCEventManager $eventManager;

    public function __destruct()
    {
        $this->close();
    }

    public function __construct(NFCInterface $nfc, ContextProxyInterface $ffi, NFCEventManager $eventManager, string $driverClassName)
    {
        /**
         * @var FFIContextProxy $ffi
         */
        $this->nfc = $nfc;
        $this->ffi = $ffi;
        $this->eventManager = $eventManager;
        $this->output = new NFCOutput($this);
        $this->driver = new $driverClassName($this);
    }

    public function close(): void
    {
        try {
            $this->driver->close();
        } finally {
            $this->getEventManager()
                ->dispatchEvent(
                    NFCEventManager::EVENT_CLOSE,
                    $this,
                );
        }
    }

    public function open(): void
    {
        try {
            $this->driver->open();
        } finally {
            $this->getEventManager()
                ->dispatchEvent(
                    NFCEventManager::EVENT_OPEN,
                    $this,
                );
        }
    }

    public function getFFI(): ContextProxyInterface
    {
        return $this->ffi;
    }

    public function getNFCContext(): CData
    {
        return $this->driver->getNFCContext();
    }

    public function getVersion(): string
    {
        return $this->driver->getVersion();
    }

    public function getDevices(bool $includeCannotOpenDevices = false): array
    {
        return $this->driver->getDevices($includeCannotOpenDevices);
    }

    public function findDeviceNameContain(string $deviceName): NFCDeviceInterface
    {
        return $this->driver->findDeviceNameContain($deviceName);
    }

    public function start(NFCDeviceInterface $device = null, NFCModulations $modulations = null): void
    {
        $this->driver->start($device, $modulations);
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function getBaudRates(): NFCBaudRates
    {
        return $this->driver->getBaudRates();
    }

    public function getModulationsTypes(): NFCModulationTypes
    {
        return $this->driver->getModulationsTypes();
    }

    public function getOutput(): NFCOutput
    {
        return $this->output;
    }

    public function getEventManager(): NFCEventManager
    {
        return $this->eventManager;
    }

    public function getNFC(): NFCInterface
    {
        return $this->nfc;
    }
}

