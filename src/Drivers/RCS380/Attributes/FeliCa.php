<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380\Attributes;

use NFC\Attributes\AbstractNFCTargetAttribute;

class FeliCa extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        return [
            'nfcid2' => sprintf(
                '%02X%02X%02X%02X%02X%02X%02X%02X',
                ...array_map('ord', array_slice(str_split($this->target->getPacket()), 17, 8))
            ),
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'nfcid2';
    }
}
