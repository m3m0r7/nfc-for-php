<?php

declare(strict_types=1);

namespace Tests\Mock;

use NFC\Drivers\LibNFC\Kernel;

class MockedLibNFCKernel extends Kernel
{
    protected string $driverClassName = MockedLibNFCDriver::class;
    protected string $FFIContextProxyClassName = MockedFFIContextProxy::class;
}
