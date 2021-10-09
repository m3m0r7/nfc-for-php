<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class Dep extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $ffi = $this->target
            ->getNFCContext()
            ->getFFI();

        return [
            'nfcid3' => $ffi->nti->ndi->abtNFCID3,
            'bs' => (int) $ffi->nti->ndi->btBS,
            'br' => (int) $ffi->nti->ndi->btBR,
            'to' => (int) $ffi->nti->ndi->btTO,
            'pp' => (int) $ffi->nti->ndi->btPP,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'nfcid3';
    }
}
