<?php
declare(strict_types=1);

namespace NFC;

class NFCDeviceInfo
{
    protected string $connectionTarget;
    protected NFCDeviceInterface $nfcDevice;

    public function __construct(string $connectionTarget, NFCDeviceInterface $nfcDevice)
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

    public function getDevice(): NFCDeviceInterface
    {
        return $this->nfcDevice;
    }
}