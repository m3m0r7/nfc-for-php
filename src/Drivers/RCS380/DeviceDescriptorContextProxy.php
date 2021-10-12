<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use NFC\Contexts\ContextAccessible;
use NFC\Contexts\ContextProxyInterface;

class DeviceDescriptorContextProxy implements ContextProxyInterface
{
    use ContextAccessible;
}
