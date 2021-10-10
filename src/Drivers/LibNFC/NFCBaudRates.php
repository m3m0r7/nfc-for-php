<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC;

use NFC\ContextEnum;
use NFC\NFCBaudRatesInterface;
use Throwable;

/**
 *
 * @property-read $NBR_UNDEFINED
 * @property-read $NBR_106
 * @property-read $NBR_212
 * @property-read $NBR_424
 * @property-read $NBR_847
 */
class NFCBaudRates extends ContextEnum implements NFCBaudRatesInterface
{
    public function getValues(): array
    {
        return [
            'NBR_UNDEFINED' => $this->ffi->NBR_UNDEFINED,
            'NBR_106' => $this->ffi->NBR_106,
            'NBR_212' => $this->ffi->NBR_212,
            'NBR_424' => $this->ffi->NBR_424,
            'NBR_847' => $this->ffi->NBR_847,
        ];
    }
}
