<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class ISO14443B2CT extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        return [
            'uid' => ($this->context->nci->abtUID[3] << 24) + ($this->context->nci->abtUID[2] << 16) + ($this->context->nci->abtUID[1] << 8) + $this->context->nci->abtUID[0],
            'product_code' => $this->context->nci->btProdCode,
            'fab_code' => $this->context->nci->btFabCode,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'uid';
    }
}
