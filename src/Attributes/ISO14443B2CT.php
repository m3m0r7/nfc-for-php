<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class ISO14443B2CT extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $ffi = $this->target
            ->getNFCContext()
            ->getFFI();

        return [
            'uid' => ($ffi->nti->nci->abtUID[3] << 24) + ($ffi->nti->nci->abtUID[2] << 16) + ($ffi->nti->nci->abtUID[1] << 8) + $ffi->nti->nci->abtUID[0],
            'product_code' => $ffi->nti->nci->btProdCode,
            'fab_code' => $ffi->nti->nci->btFabCode,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'uid';
    }
}
