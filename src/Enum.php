<?php
declare(strict_types=1);

namespace NFC;

use NFC\Contexts\ContextProxyInterface;

abstract class Enum
{
    protected ContextProxyInterface $ffi;

    public function __construct(ContextProxyInterface $ffi)
    {
        $this->ffi = $ffi;
    }
    public function __get(string $name)
    {
        return $this->getEnums()[$name] ?? null;
    }

    abstract public function getEnums(): array;
}