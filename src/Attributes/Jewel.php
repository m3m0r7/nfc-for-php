<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

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
