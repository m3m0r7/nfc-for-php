<?php
declare(strict_types=1);

namespace NFC\Contexts;

use FFI\CData;
use NFC\NFCTarget;

class NFCTargetContextProxy implements ContextProxyInterface
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
}
