<?php
declare(strict_types=1);

namespace NFC;

use Throwable;

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

class NFCModulationTypes extends Enum
{
    public function getEnums(): array
    {
        return [
            'NMT_ISO14443A' => $this->ffi->NMT_ISO14443A,
            'NMT_JEWEL' => $this->ffi->NMT_JEWEL,
            'NMT_ISO14443B' => $this->ffi->NMT_ISO14443B,
            'NMT_ISO14443BI' => $this->ffi->NMT_ISO14443BI,
            'NMT_ISO14443B2SR' => $this->ffi->NMT_ISO14443B2SR,
            'NMT_ISO14443B2CT' => $this->ffi->NMT_ISO14443B2CT,
            'NMT_FELICA' => $this->ffi->NMT_FELICA,
            'NMT_DEP' => $this->ffi->NMT_DEP,
            'NMT_BARCODE' => $this->ffi->NMT_BARCODE,
            'NMT_ISO14443BICLASS' => $this->ffi->NMT_ISO14443BICLASS,
        ];
    }
}
