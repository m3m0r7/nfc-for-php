<?php

declare(strict_types=1);

namespace NFC\Drivers\Emulator;

use NFC\NFCModulationTypesInterface;

/**
 *
 * @property-read $NMT_ISO14443A
 * @property-read $NMT_JEWEL
 * @property-read $NMT_ISO14443B
 * @property-read $NMT_ISO14443BI
 * @property-read $NMT_ISO14443B2SR
 * @property-read $NMT_ISO14443B2CT
 * @property-read $NMT_FELICA
 * @property-read $NMT_DEP
 * @property-read $NMT_BARCODE
 * @property-read $NMT_ISO14443BICLASS
 */

class EmulateNFCModulationTypes implements NFCModulationTypesInterface
{
    public function getValues(): array
    {
        return [
            'NMT_ISO14443A' => 1,
            'NMT_JEWEL' => 2,
            'NMT_ISO14443B' => 3,
            'NMT_ISO14443BI' => 4,
            'NMT_ISO14443B2SR' => 5,
            'NMT_ISO14443B2CT' => 6,
            'NMT_FELICA' => 7,
            'NMT_DEP' => 8,
            'NMT_BARCODE' => 9,
            'NMT_ISO14443BICLASS' => 10,
        ];
    }
}
