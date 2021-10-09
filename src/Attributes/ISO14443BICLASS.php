<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class ISO14443BICLASS extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $ffi = $this->target
            ->getNFCContext()
            ->getFFI();

        return [
            'uid' => $ffi->nti->nhi->abtUID,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'uid';
    }
}
