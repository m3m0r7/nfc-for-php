<?php

declare(strict_types=1);

namespace NFC;

use NFC\Collections\NFCModulations;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\FFIContextProxy;
use NFC\Drivers\DriverInterface;

/**
 * @method array getDevices(bool $includeCannotOpenDevices = false)
 * @method NFCDeviceInterface findDeviceNameContain(string $deviceName)
 * @method string getVersion()
 * @method void start(NFCDeviceInterface $device = null, NFCModulations $modulations = null)
 * @method NFCBaudRatesInterface getBaudRates()
 * @method NFCModulationTypesInterface getModulationsTypes()
 */
class NFCContext
{
    protected NFCInterface $NFC;
    protected ContextProxyInterface $FFI;
    protected NFCOutput $output;
    protected DriverInterface $driver;
    protected NFCEventManager $eventManager;

    public function __destruct()
    {
        $this->close();
    }

    public function __construct(NFCInterface $NFC, ContextProxyInterface $FFI, NFCEventManager $eventManager, string $driverClassName)
    {
        /**
         * @var FFIContextProxy $FFI
         */
        $this->NFC = $NFC;
        $this->FFI = $FFI;
        $this->eventManager = $eventManager;
        $this->output = new NFCOutput($this);
        $this->driver = new $driverClassName($this);
    }

    public function __call($name, $arguments)
    {
        try {
            return $this->driver->{$name}(...$arguments);
        } catch (NFCException $e) {
            $this->NFC
                ->getLogger()
                ->error((string) $e);

            throw $e;
        }
    }

    public function close(): void
    {
        $this->NFC->getLogger()->info(
            'Close the NFC context'
        );

        try {
            $this->driver->close();
        } catch (NFCException $e) {
            $this->NFC
                ->getLogger()
                ->error((string) $e);

            throw $e;
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
        $this->NFC->getLogger()->info(
            'Open a NFC context'
        );

        try {
            $this->driver->open();
        } catch (NFCException $e) {
            $this->NFC
                ->getLogger()
                ->error((string) $e);

            throw $e;
        } finally {
            $this->getEventManager()
                ->dispatchEvent(
                    NFCEventManager::EVENT_OPEN,
                    $this,
                );
        }
    }

    /**
     * @return ContextProxyInterface|FFIContextProxy
     */
    public function getFFI(): ContextProxyInterface
    {
        return $this->FFI;
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
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
        return $this->NFC;
    }
}
