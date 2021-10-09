<?php
declare(strict_types=1);

namespace NFC;

class NFCDevice
{
    protected string $connection;
    protected NFCContext $context;
    protected $deviceContext = null;

    public function __construct(NFCContext $context)
    {
        $this->context = $context;
    }

    public function close()
    {
        $this->validateDeviceOpened();

        $this
            ->context
            ->getFFI()
            ->nfc_close($this->deviceContext);

        $this->deviceContext = null;
    }

    public function open(?string $connection = null): self
    {
        if ($this->deviceContext !== null) {
            throw new NFCDeviceException('NFC device already opened.');
        }

        $this->deviceContext = $this
            ->context
            ->getFFI()
            ->nfc_open(
                $this->context->getNFCContext(),
                $connection
            );

        if ($this->deviceContext === null) {
            throw new NFCDeviceException(
                "Unable to open NFC device" . ($connection !== null ? " [{$connection}]" : '')
            );
        }

        if ($this->context->getFFI()->nfc_initiator_init($this->deviceContext) < 0) {
            $this
                ->context
                ->getFFI()
                ->nfc_perror(
                    $this->deviceContext,
                    "nfc_initiator_init"
                );

            throw new NFCDeviceException(
                "Cannot initialize NFC device" . ($connection !== null ? " [{$connection}]" : '')
            );
        }

        return $this;
    }

    public function getDeviceName(): string
    {
        $this->validateDeviceOpened();

        return $this->context
            ->getFFI()
            ->nfc_device_get_name(
                $this->deviceContext
            );
    }

    public function getDeviceContext()
    {
        return $this->deviceContext;
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