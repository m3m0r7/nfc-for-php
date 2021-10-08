<?php
declare(strict_types=1);

namespace NFC;

abstract class Enum
{
    protected \FFI $ffi;

    public function __construct(\FFI $ffi)
    {
        $this->ffi = $ffi;
    }
    public function __get(string $name)
    {
        return $this->getEnums()[$name] ?? null;
    }

    abstract public function getEnums(): array;
}