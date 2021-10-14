<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380\Attributes;

use NFC\Attributes\AbstractNFCTargetAttribute;
use NFC\Drivers\RCS380\ReceivePacketException;
use NFC\Util\Util;

class FeliCa extends AbstractNFCTargetAttribute
{
    public function getAttributes(): array
    {
        $IDmPacket = array_slice(str_split($this->target->getPacket()), 17, 8);

        if (count($IDmPacket) < 8) {
            throw new ReceivePacketException("Packet is invalid [" . Util::toHex(implode($IDmPacket)) . "]");
        }

//        var_dump(Util::toHex($this->target->getPacket()));
        return [
            'nfcid2' => sprintf(
                '%02X%02X%02X%02X%02X%02X%02X%02X',
                ...array_map('ord', $IDmPacket)
            ),
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'nfcid2';
    }
}
