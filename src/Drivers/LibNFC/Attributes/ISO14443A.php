<?php
declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Attributes\AbstractNFCTargetAttribute;

class ISO14443A extends AbstractNFCTargetAttribute
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
