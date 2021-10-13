<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Drivers\LibNFC\LibNFCAttribute;

class ISO14443BI extends LibNFCAttribute
{
    public function getAttributes(): array
    {
        return [
            'div' => $this->context->nii->abtDIV,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'div';
    }
}
