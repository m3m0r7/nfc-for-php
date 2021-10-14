<?php

declare(strict_types=1);

namespace NFC\Attributes;

use FFI\CData;
use NFC\NFCAttributeException;
use NFC\NFCTargetInterface;

abstract class AbstractNFCTargetAttribute implements NFCTargetAttributeInterface
{
    protected NFCTargetInterface $target;

    public function __construct(NFCTargetInterface $target)
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
        return $this->get($this->getIDAttributeName());
    }

    public function get(string $name)
    {
        return (string) ($this->getAttributes()[$name] ?? '(null)');
    }

    public function __toString()
    {
        return "IDm: {$this->getID()}";
    }
}
