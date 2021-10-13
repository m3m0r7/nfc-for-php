<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Drivers\LibNFC\LibNFCAttribute;

class ISO14443B extends LibNFCAttribute
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
