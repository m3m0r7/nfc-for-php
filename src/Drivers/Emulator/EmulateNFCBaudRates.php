<?php

declare(strict_types=1);

namespace NFC\Drivers\Emulator;

use NFC\NFCBaudRatesInterface;

/**
 *
 * @property-read $NBR_UNDEFINED
 * @property-read $NBR_106
 * @property-read $NBR_212
 * @property-read $NBR_424
 * @property-read $NBR_847
 */
class EmulateNFCBaudRates implements NFCBaudRatesInterface
{
    public function getValues(): array
    {
        return [
            'NBR_UNDEFINED' => 0,
            'NBR_106' => 1,
            'NBR_212' => 2,
            'NBR_424' => 3,
            'NBR_847' => 4,
        ];
    }
}
