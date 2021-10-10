<?php

declare(strict_types=1);

namespace Tests\Mock;

use NFC\Attributes\NFCTargetAttributeInterface;
use NFC\Drivers\Emulator\EmulateAttribute;
use NFC\Drivers\LibNFC\NFCTarget;

class MockedNFCTarget extends NFCTarget
{
    public function getAttributeAccessor(): NFCTargetAttributeInterface
    {
        return new EmulateAttribute();
    }
}
