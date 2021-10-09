<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class ISO14443B extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $ffi = $this->target
            ->getNFCContext()
            ->getFFI();

        return [
            'pupi' => $ffi->nti->nbi->abtPupi,
            'application_data' => $ffi->nti->nbi->abtApplicationData,
            'protocol_info' => $ffi->nti->nbi->abtProtocolInfo,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'pupi';
    }
}
