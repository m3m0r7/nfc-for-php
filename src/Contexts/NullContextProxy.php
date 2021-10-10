<?php

declare(strict_types=1);

namespace NFC\Contexts;

use FFI\CData;
use NFC\NFCTarget;

class NullContextProxy implements ContextProxyInterface
{
    public function __get($name)
    {
        return null;
    }
}
