<?php

declare(strict_types=1);

namespace Tests\Mock;

use NFC\Drivers\RCS380\NFCDevice;

class MockedRCS380NFCDevice extends NFCDevice
{
    protected ?string $deviceName = 'dummy-device';

    public function getTransportEndpoint(): array
    {
        return [
            'in' => [0],
            'out' => [1],
        ];
    }
}
