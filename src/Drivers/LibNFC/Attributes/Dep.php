<?php
declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Attributes;

use FFI\CData;
use NFC\Attributes\AbstractNFCTargetAttribute;

class Dep extends AbstractNFCTargetAttribute
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
