<?php

declare(strict_types=1);

namespace NFC\Drivers\Emulator;

use NFC\Attributes\NFCTargetAttributeInterface;

class EmulateAttribute implements NFCTargetAttributeInterface
{
    public function getAttributes(): array
    {
        return [
            'id' => $this->getID(),
        ];
    }

    public function getID(): string
    {
        return '12345678';
    }
}
