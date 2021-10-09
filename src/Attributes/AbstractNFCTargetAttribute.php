<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCAttributeException;
use NFC\NFCTarget;

abstract class AbstractNFCTargetAttribute implements NFCTargetAttributeInterface
{
    protected NFCTarget $target;

    public function __construct(NFCTarget $target)
    {
        $this->target = $target;
    }

    abstract public function getAttributes(): array;

    public function getIDAttributeName(): ?string
    {
        return null;
    }

    public function getID(): string
    {
        if ($this->getIDAttributeName() === null) {
            throw new NFCAttributeException('The target type has not an ID.');
        }
        return $this->getAttributes()[$this->getIDAttributeName()];
    }
}
