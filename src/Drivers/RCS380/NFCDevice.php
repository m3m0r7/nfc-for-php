<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use FFI\CData;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NFCDeviceContextProxy;
use NFC\Drivers\LibNFC\Headers\NFCConstants;
use NFC\NFCContext;
use NFC\NFCDeviceException;
use NFC\NFCDeviceInterface;
use NFC\NFCException;
use NFC\Util\Util;

class NFCDevice implements NFCDeviceInterface
{
    protected string $connection;
    protected NFCContext $NFCContext;
    protected ?CData $deviceContext = null;
    protected ?ContextProxyInterface $device = null;
    protected ?ContextProxyInterface $descriptor = null;
    protected ?NFCDeviceContextProxy $deviceContextProxy = null;
    protected ?string $deviceName = null;
    protected int $lastError = 0;

    public function __construct(NFCContext $NFCContext, ContextProxyInterface $selectedDevice, ContextProxyInterface $deviceDescriptor)
    {
        $this->NFCContext = $NFCContext;

        $this->device = $selectedDevice;
        $this->descriptor = $deviceDescriptor;
    }

    public function close(): void
    {
        $this->validateDeviceOpened();

        $this->NFCContext
            ->getFFI()
            ->libusb_release_interface($this->deviceContext, 0);

        $this->NFCContext
            ->getFFI()
            ->libusb_close($this->deviceContext);

        $this->deviceContext = null;
    }

    public function open(?string $connection = null, bool $forceOpen = false): self
    {
        if ($this->deviceContext !== null) {
            throw new NFCDeviceException('NFC device already opened.');
        }

        $this->connection = $connection;

        $this->deviceContext = $this
            ->NFCContext
            ->getFFI()
            ->new('libusb_device_handle *');

        $this->lastError = $this
            ->NFCContext
            ->getFFI()
            ->libusb_open(
                $this->device->getContext(),
                \FFI::addr($this->deviceContext)
            );

        if ($this->lastError < 0 && !$forceOpen) {
            throw new NFCDeviceException('Cannot open a device [' . $this->lastError . ']');
        }

        return $this;
    }

    public function getDeviceName(): string
    {
        if ($this->deviceContext === null) {
            return 'Unknown device';
        }

        if ($this->deviceName !== null) {
            return $this->deviceName;
        }

        $fetchKeys = [
            'iManufacturer' => null,
            'iProduct' => null,
            'iSerialNumber' => null
        ];

        $tmpUInt8Array = $this
            ->NFCContext
            ->getFFI()
            ->new('uint8_t[255]');

        $arraySize = \FFI::sizeof($tmpUInt8Array);

        foreach (array_keys($fetchKeys) as $key) {
            $this->lastError = $this
                ->NFCContext
                ->getFFI()
                ->libusb_get_string_descriptor_ascii(
                    $this->deviceContext,
                    $this->descriptor
                        ->{$key}
                        ->cdata,
                    $tmpUInt8Array,
                    $arraySize
                );

            if ($this->lastError < 0) {
                continue;
            }

            $fetchKeys[$key] = Util::itoa($tmpUInt8Array);
        }

        return $this->deviceName = "{$fetchKeys['iManufacturer']} {$fetchKeys['iProduct']} [{$fetchKeys['iSerialNumber']}]";
    }

    public function isOpened(): bool
    {
        return $this->deviceContext !== null;
    }

    public function getLastErrorCode(): int
    {
        $this->validateDeviceOpened();

        return $this->lastError;
    }

    public function getLastErrorName(): string
    {
        return 'UNKNOWN ERROR';
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function getDeviceContext(): ContextProxyInterface
    {
        return $this->deviceContextProxy ??= new NFCDeviceContextProxy($this->deviceContext);
    }

    public function getPortIn(): int
    {
        return 0;
    }

    public function getPortOut(): int
    {
        return 0;
    }

    protected function validateDeviceOpened(): void
    {
        if ($this->deviceContext === null) {
            throw new NFCException(
                "NFC Device not initialized. Please run `NFCDevice::open` before."
            );
        }
    }
}
