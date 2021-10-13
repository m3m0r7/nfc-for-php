<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use NFC\Collections\NFCModulations;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NullContextProxy;
use NFC\Drivers\DriverInterface;
use NFC\Drivers\RCS380\Headers\LibUSBConstants;
use NFC\NFCBaudRatesInterface;
use NFC\NFCContext;
use NFC\NFCDeviceException;
use NFC\NFCDeviceInfo;
use NFC\NFCDeviceInterface;
use NFC\NFCDeviceNotFoundException;
use NFC\NFCEventManager;
use NFC\NFCException;
use NFC\NFCModulationTypesInterface;
use NFC\NFCTargetInterface;
use NFC\NFCTargetTimeoutException;
use NFC\Util\ReaderAdjustable;
use NFC\Util\ReaderPollable;
use NFC\Util\ReaderReleasable;

class RCS380Driver implements DriverInterface
{
    use ReaderPollable;
    use ReaderAdjustable;
    use ReaderReleasable;

    protected const VENDOR_ID = 0x054C;
    protected const PRODUCT_ID = 0x06C3;

    protected NFCContext $NFCContext;
    protected bool $isOpened = false;
    protected ?string $lastResponsePacket = null;
    protected ?RCS380Command $commandInterface = null;

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
        if ($device === null) {
            $deviceInfo = $this->getDevices()[0] ?? null;

            if ($deviceInfo === null) {
                throw new NFCException('Available NFC device not found');
            }

            $device = $deviceInfo->getDevice();
        }

        if (!($device instanceof NFCDevice)) {
            throw new NFCDeviceException('Invalid NFCDevice type (' . get_class($device) . ')');
        }

        $this->NFCContext->getEventManager()
            ->dispatchEvent(
                NFCEventManager::EVENT_START,
                $this->NFCContext,
                $device
            );

        $this->commandInterface = new RCS380Command(
            $this->NFCContext,
            $device
        );

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Start to listen on device {$device->getDeviceName()} [{$device->getConnection()}]");

        $this->commandInterface->init();
        $this->commandInterface->setCommandType();
        $this->commandInterface->switchRF();
        $this->commandInterface->inSetRF();
        $this->commandInterface->inSetProtocol1();
        $this->commandInterface->inSetProtocol2();

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Communication succeeded");

        $touched = null;

        do {
            try {
                $this->lastResponsePacket = $this->commandInterface->sensfReq();
                if ($this->lastResponsePacket === null) {
                    $this->NFCContext->getEventManager()
                        ->dispatchEvent(
                            NFCEventManager::EVENT_MISSING,
                            $this->NFCContext,
                            $device
                        );
                    usleep($this->pollingInterval);
                    continue;
                }

                if (substr($this->lastResponsePacket, 0, strlen(RCS380Command::MAGIC)) !== RCS380Command::MAGIC) {
                    continue;
                }

                $target = new NFCTarget($this->NFCContext, $device, $this->lastResponsePacket);

                $info = [
                    'class' => get_class(
                        $target
                            ->getAttributeAccessor()
                    ),
                    'id' => $target
                        ->getAttributeAccessor()
                        ->getID()
                ];

                $isTouchedMoment = false;

                if ($touched !== null && $info['id'] === $touched['id']) {
                    if ($touched['expires'] > time()) {
                        $isTouchedMoment = true;
                    }
                }

                if ($isTouchedMoment) {
                    continue;
                }

                $this->NFCContext
                    ->getEventManager()
                    ->dispatchEvent(
                        NFCEventManager::EVENT_TOUCH,
                        $this->NFCContext,
                        $target
                    );

                $this->NFCContext
                    ->getNFC()
                    ->getLogger()
                    ->info("Touched target: {$target->getAttributeAccessor()->getID()}");

                if ($this->enableContinuousTouchAdjustment) {
                    $touched = $info + [
                            'expires' => time() + $this->continuousTouchAdjustmentExpires,
                        ];
                }

                // Detect release touching
                $timeout = time() + $this->waitDidNotReleaseTimeout;
                while (!$this->isPresent($device, $target)) {
                    usleep($this->waitPresentationReleaseInterval);
                    if (time() < $timeout) {
                        throw new NFCTargetTimeoutException(
                            'Timed out because it has not been released for a long time'
                        );
                    }
                }

                $this->NFCContext
                    ->getEventManager()
                    ->dispatchEvent(
                        NFCEventManager::EVENT_RELEASE,
                        $this->NFCContext,
                        $target
                    );

                $this->NFCContext
                    ->getNFC()
                    ->getLogger()
                    ->info("Released target: {$target->getAttributeAccessor()->getID()}");
            } catch (\Throwable $e) {
                $this->NFCContext
                    ->getEventManager()
                    ->dispatchEvent(
                        NFCEventManager::EVENT_ERROR,
                        $this->NFCContext,
                        $e
                    );
            }
        } while ($this->hasNext());
    }

    public function isPresent(NFCDeviceInterface $device, NFCTargetInterface $target): bool
    {
        return $this->lastResponsePacket === $this->commandInterface->sensfReq();
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
        return new NullContextProxy();
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
