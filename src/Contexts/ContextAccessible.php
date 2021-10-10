<?php

declare(strict_types=1);

namespace NFC\Contexts;

use FFI\CData;

trait ContextAccessible
{
    protected CData $context;

    public function __construct(CData $context)
    {
        $this->context = $context;
    }

    public function getContext(): CData
    {
        return $this->context;
    }

    public function __get($name)
    {
        return $this->context->{$name} ?? null;
    }

    public function __isset($name)
    {
        return \FFI::isNull($name) !== null;
    }

    public function __unset($name)
    {
        unset($this->context->{$name});
    }
}
