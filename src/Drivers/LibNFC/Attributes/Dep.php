<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use NFC\Drivers\LibNFC\LibNFCAttribute;

class Dep extends LibNFCAttribute
{
    public function getAttributes(): array
    {
        return [
            'nfcid3' => $this->context->ndi->abtNFCID3,
            'bs' => (int) $this->context->ndi->btBS,
            'br' => (int) $this->context->ndi->btBR,
            'to' => (int) $this->context->ndi->btTO,
            'pp' => (int) $this->context->ndi->btPP,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'nfcid3';
    }
}
