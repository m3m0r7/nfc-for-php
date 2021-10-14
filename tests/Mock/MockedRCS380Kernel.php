<?php

declare(strict_types=1);

namespace Tests\Mock;

use NFC\Drivers\RCS380\Kernel;

class MockedRCS380Kernel extends Kernel
{
    protected string $driverClassName = MockedRCS380Driver::class;
    protected string $FFIContextProxyClassName = MockedRCS380FFIContextProxy::class;
}
