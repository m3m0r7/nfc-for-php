<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Drivers\LibNFC\LibNFCAttribute;

class ISO14443B2SR extends LibNFCAttribute
{
    public function getAttributes(): array
    {
        return [
            'uid' => $this->context->nci->abtUID,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'uid';
    }
}
