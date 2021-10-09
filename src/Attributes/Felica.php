<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class Felica extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $ffi = $this->target
            ->getNFCContext()
            ->getFFI();

        return [
            'nfcid2' => $ffi->nti->nfi->abtId,
            'pad' => $ffi->nti->nfi->abtPad,
            'sc' => $ffi->nti->nfi->abtSysCode,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'nfcid2';
    }
}
