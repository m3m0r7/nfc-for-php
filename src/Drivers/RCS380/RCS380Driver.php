<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use NFC\Collections\NFCModulations;
use NFC\Contexts\ContextProxyInterface;
use NFC\Drivers\DriverInterface;
use NFC\Drivers\RCS380\Headers\LibUSBConstants;
use NFC\NFCBaudRatesInterface;
use NFC\NFCContext;
use NFC\NFCDeviceException;
use NFC\NFCDeviceInfo;
use NFC\NFCDeviceInterface;
use NFC\NFCDeviceNotFoundException;
use NFC\NFCException;
use NFC\NFCModulationTypesInterface;
use NFC\NFCTargetInterface;

class RCS380Driver implements DriverInterface
{
    protected const VENDOR_ID = 0x054C;
    protected const PRODUCT_ID = 0x06C3;

    protected NFCContext $NFCContext;
    protected bool $isOpened = false;

    public function __construct(NFCContext $NFCContext)
    {
        $this->NFCContext = $NFCContext;
    }

    public function open(): DriverInterface
    {
        $this->NFCContext
            ->getFFI()
            ->libusb_init(null);

        $this->isOpened = true;

        return $this;
    }

    public function close(): void
    {
        $this->NFCContext
            ->getFFI()
            ->libusb_exit(null);
    }

    public function getVersion(): string
    {
        return "libusb-" . sprintf('0x%08X', LibUSBConstants::LIBUSB_API_VERSION);
    }

    public function getDevices(bool $includeCannotOpenDevices = false): array
    {
        $this->validateContextOpened();

        $devices = $this->NFCContext
            ->getFFI()
            ->new('libusb_device **');

        $size = $this->NFCContext
            ->getFFI()
            ->libusb_get_device_list(
                null,
                \FFI::addr($devices)
            );

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Connected USB devices: {$size}");

        try {
            $deviceInfo = [];
            for ($i = 0; $i < $size; $i++) {
                $selectedDevice = $devices[$i];

                $deviceDescriptor = $this
                    ->NFCContext
                    ->getFFI()
                    ->new('libusb_device_descriptor');

                $errorCode = $this->NFCContext
                    ->getFFI()
                    ->libusb_get_device_descriptor(
                        $selectedDevice,
                        \FFI::addr($deviceDescriptor)
                    );

                if ($errorCode < 0) {
                    throw new NFCDeviceException('Cannot get device descriptor [' . $errorCode . ']');
                }

                if ($deviceDescriptor->idVendor !== static::VENDOR_ID && $deviceDescriptor->idProduct !== static::PRODUCT_ID) {
                    // Not use other devices.
                    continue;
                }

                $connection = sprintf(
                    "pn53x_usb:%03s:%03s",
                    (int) $this->NFCContext
                        ->getFFI()
                        ->libusb_get_bus_number($selectedDevice),
                    (int) $this->NFCContext
                        ->getFFI()
                        ->libusb_get_device_address($selectedDevice),
                );

                $deviceInfo[] = new NFCDeviceInfo(
                    $connection,
                    (new NFCDevice(
                        $this->NFCContext,
                        new SelectedDeviceContextProxy($selectedDevice),
                        new DeviceDescriptorContextProxy($deviceDescriptor),
                    ))->open($connection, $includeCannotOpenDevices),
                );
            }
        } finally {
            $this->NFCContext
                ->getFFI()
                ->libusb_free_device_list($devices, 1);
        }

        return $deviceInfo;
    }

    public function findDeviceName(string $deviceName): NFCDeviceInterface
    {
        $this->validateContextOpened();

        $exceptions = [];
        try {
            /**
             * @var NFCDeviceInfo $NFCDevice
             */
            foreach ($this->getDevices() as $NFCDevice) {
                if (strpos($NFCDevice->getDeviceName(), $deviceName) !== false) {
                    return $NFCDevice->getDevice();
                }
            }
        } catch (NFCDeviceException $e) {
            $exceptions[] = $e;
        }

        if (count($exceptions) === 0) {
            throw new NFCDeviceNotFoundException('Unable to find available device');
        }

        throw new NFCException(
            implode(
                "\n",
                array_map(
                    fn (string $exception) => (string) $exception,
                    $exceptions
                )
            )
        );
    }

    public function start(NFCDeviceInterface $device = null, NFCModulations $modulations = null): void
    {
        throw new NFCException('This method (' . __METHOD__ . ') is not implemented yet');
    }

    public function isPresent(NFCDeviceInterface $device, NFCTargetInterface $target): bool
    {
        throw new NFCException('This method (' . __METHOD__ . ') is not implemented yet');
    }

    public function getBaudRates(): NFCBaudRatesInterface
    {
        throw new NFCException('This method (' . __METHOD__ . ') is not implemented yet');
    }

    public function getModulationsTypes(): NFCModulationTypesInterface
    {
        throw new NFCException('This method (' . __METHOD__ . ') is not implemented yet');
    }

    public function getNFCContext(): ContextProxyInterface
    {
        throw new NFCException('You cannot call the ' . __METHOD__);
    }

    private function validateContextOpened()
    {
        // Open context automatically.
        if (!$this->isOpened) {
            $this->NFCContext->open();
        }

        if ($this->NFCContext === null) {
            throw new NFCException('Closed the context');
        }
    }
}
