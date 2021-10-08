<?php
declare(strict_types=1);

namespace NFC;

class NFCDeviceInfo
{
    protected string $connectionTarget;
    protected NFCDevice $nfcDevice;

    public function __construct(string $connectionTarget, NFCDevice $nfcDevice)
    {
        $this->connectionTarget = $connectionTarget;
        $this->nfcDevice = $nfcDevice;
    }

    public function getConnectionTarget(): string
    {
        return $this->connectionTarget;
    }

    public function getDeviceName(): string
    {
        return $this->nfcDevice->getDeviceName();
    }

    public function getDevice(): NFCDevice
    {
        return $this->nfcDevice;
    }
}