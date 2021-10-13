<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Drivers\LibNFC\LibNFCAttribute;

class ISO14443BICLASS extends LibNFCAttribute
{
    public function getAttributes(): array
    {
        return [
            'uid' => $this->context->nhi->abtUID,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'uid';
    }
}
