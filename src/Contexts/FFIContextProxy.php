<?php
declare(strict_types=1);

namespace NFC\Contexts;

use FFI\CData;
use NFC\NFCTarget;

/**
 * @mixin \FFI
 */
class FFIContextProxy implements ContextProxyInterface
{
    protected \FFI $ffi;

    public function __construct(\FFI $ffi)
    {
        $this->ffi = $ffi;
    }

    public function __get($name)
    {
        return $this->ffi->{$name} ?? null;
    }

    public function __call($name, $arguments)
    {
        return $this->ffi->{$name}(...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return \FFI::{$name}($arguments);
    }

    public function __set($name, $value)
    {
        $this->ffi->{$name} = $value;
    }
}
