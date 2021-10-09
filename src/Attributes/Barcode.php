<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class Barcode extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $ffi = $this->target
            ->getNFCContext()
            ->getFFI();

        $data = [];

        for ($i = 0; $i < $ffi->nti->nti->szDataLen; $i++) {
            $data[] = $ffi->nti->nti->abtData[$i];
        }

        return [
            'data' => $data,
        ];
    }
}
