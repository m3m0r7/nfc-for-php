<?php
declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCAttributeException;
use NFC\NFCTarget;
use NFC\NFCTargetInterface;

abstract class AbstractNFCTargetAttribute implements NFCTargetAttributeInterface
{
    protected NFCTargetInterface $target;
    protected CData $context;

    public function __construct(NFCTargetInterface $target)
    {
        $this->target = $target;
        $this->context = $target
            ->getNFCTargetContext()
            ->nti;
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
        return (string) $this->getAttributes()[$this->getIDAttributeName()];
    }
}
