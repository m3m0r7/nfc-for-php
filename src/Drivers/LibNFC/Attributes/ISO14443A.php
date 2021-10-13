<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Drivers\LibNFC\LibNFCAttribute;

class ISO14443A extends LibNFCAttribute
{
    public function getAttributes(): array
    {
        return [
            'atqa' => $this->context->nai->abtAtqa,
            'uid' => $this->context->nai->abtUid,
            'sak' => $this->context->nai->abtSak,
            'ats' => $this->context->nai->abtAts,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'uid';
    }
}
