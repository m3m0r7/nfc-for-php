<?php

declare(strict_types=1);

namespace NFC;

class NFCDeviceInfo
{
    protected string $connectionTarget;
    protected NFCDeviceInterface $NFCDevice;

    public function __construct(string $connectionTarget, NFCDeviceInterface $NFCDevice)
    {
        $this->connectionTarget = $connectionTarget;
        $this->NFCDevice = $NFCDevice;
    }

    public function getConnectionTarget(): string
    {
        return $this->connectionTarget;
    }

    public function getDeviceName(): string
    {
        return $this->NFCDevice->getDeviceName();
    }

    public function getDevice(): NFCDeviceInterface
    {
        return $this->NFCDevice;
    }
}
