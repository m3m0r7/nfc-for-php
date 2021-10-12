<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use NFC\ContextEnum;
use NFC\NFCModulationTypesInterface;

/**
 *
 * @property-read $FeliCa
 */

class NFCModulationTypes extends ContextEnum implements NFCModulationTypesInterface
{
    public function getValues(): array
    {
        return [
            'FeliCa' => 1,
        ];
    }
}
