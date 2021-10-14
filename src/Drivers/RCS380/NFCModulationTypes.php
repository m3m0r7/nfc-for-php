<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use NFC\ContextEnum;
use NFC\NFCModulationTypesInterface;

/**
 *
 * @property-read $NMT_FELICA
 */

class NFCModulationTypes extends ContextEnum implements NFCModulationTypesInterface
{
    public function getValues(): array
    {
        return [
            'NMT_FELICA' => 1,
        ];
    }
}
