<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class ISO14443A extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $ffi = $this->target
            ->getNFCContext()
            ->getFFI();

        return [
            'atqa' => $ffi->nti->nai->abtAtqa,
            'uid' => $ffi->nti->nai->abtUid,
            'sak' => $ffi->nti->nai->abtSak,
            'ats' => $ffi->nti->nai->abtAts,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'uid';
    }
}
