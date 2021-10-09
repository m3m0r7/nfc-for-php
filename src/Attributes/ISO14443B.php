<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class ISO14443B extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        return [
            'pupi' => $this->context->nbi->abtPupi,
            'application_data' => $this->context->nbi->abtApplicationData,
            'protocol_info' => $this->context->nbi->abtProtocolInfo,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'pupi';
    }
}
