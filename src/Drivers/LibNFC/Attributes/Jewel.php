<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Drivers\LibNFC\LibNFCAttribute;

class Jewel extends LibNFCAttribute
{
    public function getAttributes(): array
    {
        return [
            'jewel_id' => $this->context->nji->btId,
            'atqa' => $this->context->nji->btSensRes,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'jewel_id';
    }
}
