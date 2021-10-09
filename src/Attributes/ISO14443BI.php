<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class ISO14443BI extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $ffi = $this->target
            ->getNFCContext()
            ->getFFI();

        return [
            'div' => $ffi->nti->nii->abtDIV,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'div';
    }
}
