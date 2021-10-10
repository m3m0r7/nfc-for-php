<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Attributes\AbstractNFCTargetAttribute;

class ISO14443BICLASS extends AbstractNFCTargetAttribute
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
