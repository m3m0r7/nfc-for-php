<?php

declare(strict_types=1);

namespace Tests\Mock;

use NFC\Drivers\RCS380\RCS380Driver;
use NFC\NFCDeviceInterface;
use NFC\NFCTargetInterface;

class MockedRCS380Driver extends RCS380Driver
{
    protected string $NFCDeviceClassName = MockedRCS380NFCDevice::class;

    public function isPresent(NFCDeviceInterface $device, NFCTargetInterface $target): bool
    {
        return true;
    }

    protected function hasNext(): bool
    {
        return false;
    }
}
