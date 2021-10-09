<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;

interface NFCTargetAttributeInterface
{
    public function getAttributes(): array;
    public function getID(): string;
}