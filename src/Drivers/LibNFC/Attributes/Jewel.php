<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Attributes\AbstractNFCTargetAttribute;

class Jewel extends AbstractNFCTargetAttribute
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
