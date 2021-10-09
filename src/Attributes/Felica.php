<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCTarget;

class Felica extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        return [
            'nfcid2' => sprintf(
                '%02X%02X%02X%02X%02X%02X%02X%02X',
                $this->context->nfi->abtId[0],
                $this->context->nfi->abtId[1],
                $this->context->nfi->abtId[2],
                $this->context->nfi->abtId[3],
                $this->context->nfi->abtId[4],
                $this->context->nfi->abtId[5],
                $this->context->nfi->abtId[6],
                $this->context->nfi->abtId[7],
            ),
            'pad' => $this->context->nfi->abtPad,
            'sc' => $this->context->nfi->abtSysCode,
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'nfcid2';
    }
}
