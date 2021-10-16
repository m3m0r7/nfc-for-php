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
    protected array $transportEndpoint = [];

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

        if (\FFI::isNull($this->deviceContext) || ($this->lastError < 0 && !$forceOpen)) {
            throw new NFCDeviceException("Cannot open a device [{$this->lastError}] [{$connection}]");
        }

        $this->lastError = $this->NFCContext
            ->getFFI()
            ->libusb_set_auto_detach_kernel_driver($this->deviceContext, 1);

        $this->lastError = $this->NFCContext
            ->getFFI()
            ->libusb_set_configuration($this->deviceContext, 1);

        if ($this->lastError < 0) {
            throw new NFCDeviceException(
                "Cannot open a device [{$this->NFCContext->getFFI()->libusb_error_name($this->lastError)} ({$this->lastError})] [{$connection}]"
            );
        }

        $this->lastError = $this->NFCContext
            ->getFFI()
            ->libusb_claim_interface($this->deviceContext, 0);

        if ($this->lastError < 0) {
            throw new NFCDeviceException(
                "Cannot open a device [{$this->NFCContext->getFFI()->libusb_error_name($this->lastError)} ({$this->lastError})] [{$connection}]"
            );
        }

        $this->lastError = $this->NFCContext
            ->getFFI()
            ->libusb_set_interface_alt_setting($this->deviceContext, 0, 0);

        if ($this->lastError < 0) {
            throw new NFCDeviceException(
                "Cannot open a device [{$this->NFCContext->getFFI()->libusb_error_name($this->lastError)} ({$this->lastError})] [{$connection}]"
            );
        }

        $this->transportEndpoint = $this->getTransportEndpoint();

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

    public function getTransportIn(): int
    {
        return current($this->transportEndpoint['in']);
    }

    public function getTransportOut(): int
    {
        return current($this->transportEndpoint['out']);
    }

    protected function getTransportEndpoint(): array
    {
        $ffi = $this
            ->NFCContext
            ->getFFI();

        $descriptors = $ffi
            ->new('libusb_config_descriptor *');

        $this->lastError = $this
            ->NFCContext
            ->getFFI()
            ->libusb_get_config_descriptor(
                $this->device->getContext(),
                0,
                \FFI::addr($descriptors)
            );

        if ($this->lastError < 0) {
            throw new NFCDeviceException("An error occurred [{$this->lastError}]");
        }

        $bulkTransferEndpoints = [
            'in' => null,
            'out' => null,
        ];

        $descriptor = $descriptors[0];

        for ($i = 0; $i < $descriptor->bNumInterfaces; $i++) {
            $interface = $descriptor->interface[$i];
            for ($j = 0; $j < $interface->num_altsetting; $j++) {
                $altSetting = $interface->altsetting[$j];
                for ($k = 0; $k < $altSetting->bNumEndpoints; $k++) {
                    $endpoint = $altSetting->endpoint[$k];
                    switch ($endpoint->bmAttributes & 0x03) {
                    case $ffi->LIBUSB_TRANSFER_TYPE_BULK:
                        if (($endpoint->bEndpointAddress & 0x80) == $ffi->LIBUSB_ENDPOINT_IN) {
                            $bulkTransferEndpoints['in'][] = $endpoint->bEndpointAddress;
                        }
                        if (($endpoint->bEndpointAddress & 0x80) == $ffi->LIBUSB_ENDPOINT_OUT) {
                            $bulkTransferEndpoints['out'][] = $endpoint->bEndpointAddress;
                        }
                        $bulkTransferEndpoint = $endpoint;
                        break;
                    }
                }
            }
        }

        if ($bulkTransferEndpoints['in'] === null || $bulkTransferEndpoints['out'] === null) {
            throw new NFCDeviceException(
                "The device cannot handle bulk transfer endpoint [{$this->getDeviceName()}]"
            );
        }

        return $bulkTransferEndpoints;
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
