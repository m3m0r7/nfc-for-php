<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC;

use FFI\CData;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NFCDeviceContextProxy;
use NFC\Drivers\LibNFC\Headers\NFCConstants;
use NFC\NFCContext;
use NFC\NFCDeviceException;
use NFC\NFCDeviceInterface;
use NFC\NFCException;

class NFCDevice implements NFCDeviceInterface
{
    protected string $connection;
    protected NFCContext $context;
    protected ?CData $deviceContext = null;
    protected ?NFCDeviceContextProxy $deviceContextProxy = null;

    public function __construct(NFCContext $context)
    {
        $this->context = $context;
    }

    public function close(): void
    {
        $this->validateDeviceOpened();

        $this
            ->context
            ->getFFI()
            ->nfc_close($this->deviceContext);

        $this->deviceContext = null;
    }

    public function open(?string $connection = null, bool $forceOpen = false): self
    {
        if ($this->deviceContext !== null) {
            throw new NFCDeviceException('NFC device already opened.');
        }

        $this->connection = $connection;

        $this->deviceContext = $this
            ->context
            ->getFFI()
            ->nfc_open(
                $this->context->getNFCContext()->getContext(),
                $connection
            );

        if ($this->deviceContext === null) {
            if ($forceOpen === true) {
                return $this;
            }

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
        if ($this->deviceContext === null) {
            return 'Unknown device';
        }

        return $this->context
            ->getFFI()
            ->nfc_device_get_name(
                $this->deviceContext
            );
    }

    public function isOpened(): bool
    {
        return $this->deviceContext !== null;
    }

    public function getLastErrorCode(): int
    {
        $this->validateDeviceOpened();

        return $this->context
            ->getFFI()
            ->nfc_device_get_last_error(
                $this->deviceContext
            );
    }

    public function getLastErrorName(): string
    {
        static $constants;
        $lastErrorCode = $this->getLastErrorCode();

        $constants ??= (new \ReflectionClass(NFCConstants::class))
            ->getConstants();

        return array_search($lastErrorCode, $constants, true) ?: 'UNKNOWN ERROR';
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function getDeviceContext(): ContextProxyInterface
    {
        return $this->deviceContextProxy ??= new NFCDeviceContextProxy($this->deviceContext);
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
