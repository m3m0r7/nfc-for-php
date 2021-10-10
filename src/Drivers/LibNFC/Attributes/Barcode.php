<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Attributes\AbstractNFCTargetAttribute;

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
