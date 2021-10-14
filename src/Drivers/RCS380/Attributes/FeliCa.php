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

        return [
            'nfcid2' => sprintf(
                '%02X%02X%02X%02X%02X%02X%02X%02X',
                ...array_map('ord', $IDmPacket)
            ),
            'pad' => sprintf(
                '%02X%02X%02X%02X%02X%02X%02X%02X',
                ...array_map('ord', array_slice(str_split($this->target->getPacket()), 25, 8))
            ),
            'sc' => sprintf(
                '%02X%02X',
                ...array_map('ord', array_slice(str_split($this->target->getPacket()), 33, 2))
            ),
        ];
    }

    public function getIDAttributeName(): ?string
    {
        return 'nfcid2';
    }

    public function __toString()
    {
        $id = Util::splitHex($this->getID());
        $pad = Util::splitHex($this->get('pad'));
        $sc = Util::splitHex($this->get('sc'));

        return <<< _
                ID (NFCID2): {$id}
            Parameter (PAD): {$pad}
           System Code (SC): {$sc}
        _;
    }
}
