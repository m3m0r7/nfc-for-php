<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class Barcode extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $data = [];

        for ($i = 0; $i < $this->context->nti->szDataLen; $i++) {
            $data[] = $this->context->nti->abtData[$i];
        }

        return [
            'data' => $data,
        ];
    }
}
