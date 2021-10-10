<?php

declare(strict_types=1);

namespace NFC;

use NFC\Contexts\ContextProxyInterface;

abstract class ContextEnum
{
    protected ContextProxyInterface $ffi;

    public function __construct(ContextProxyInterface $ffi)
    {
        $this->ffi = $ffi;
    }
    public function __get(string $name)
    {
        return $this->getValues()[$name] ?? null;
    }

    abstract public function getValues(): array;
}
