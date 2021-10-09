<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class Jewel extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $ffi = $this->target
            ->getNFCContext()
            ->getFFI();

        return [
            'jewel_id' => $ffi->nti->nji->btId,
            'atqa' => $ffi->nti->nji->btSensRes,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'jewel_id';
    }
}
