<?php

declare(strict_types=1);

namespace Tests\Units\Drivers;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use NFC\Drivers\LibNFC\NFCDevice;
use NFC\NFC;
use NFC\NFCContext;
use NFC\NFCDeviceNotFoundException;
use NFC\Util\LoggerStackHandler;
use NFC\Util\OS;
use PHPUnit\Framework\TestCase;
use Tests\Mock\MockedLibNFCKernel;

class LibNFCTest extends TestCase
{
    protected ?NFC $NFC = null;
    protected ?NFCContext $NFCContext = null;
    protected LoggerStackHandler $stackHandler;

    public function setUp(): void
    {
        $this->NFC = new NFC(
            MockedLibNFCKernel::class,
            OS::isMac()
                ? '/usr/local/Cellar/libnfc/1.8.0/lib/libnfc.dylib'
                : null,
        );

        $logger = new Logger(__CLASS__);
        $logger->pushHandler($this->stackHandler = new LoggerStackHandler());
        $this->stackHandler->setFormatter(new LineFormatter('%message%'));

        $this->NFC->setLogger($logger);
        $this->NFCContext = $this->NFC->createContext();
        $this->NFCContext->open();
    }

    public function testListDevice()
    {
        $devices = $this->NFCContext->getDevices();
        $this->assertCount(2, $devices);
        $this->assertSame('dummy-device', $devices[0]->getDevice()->getDeviceName());
        $this->assertSame('dummy-1', $devices[0]->getDevice()->getConnection());
        $this->assertSame('dummy-device', $devices[1]->getDevice()->getDeviceName());
        $this->assertSame('dummy-2', $devices[1]->getDevice()->getConnection());
    }

    public function testFindDevice()
    {
        $device = $this->NFCContext->findDeviceName('dummy-de');

        $this->assertSame('dummy-device', $device->getDeviceName());
        $this->assertSame('dummy-1', $device->getConnection());
    }

    public function testFindDeviceThenNotFound()
    {
        $this->expectException(NFCDeviceNotFoundException::class);
        $this->NFCContext->findDeviceName('dummy-device-not-found');
    }

    public function testStart()
    {
        $this->NFCContext->start();

        $stacks = $this->stackHandler->getStacks();

        $this->assertContains('Start to listen on device dummy-device [dummy-1]', $stacks);
        $this->assertContains('Touched target: 12345678', $stacks);
        $this->assertContains('Released target: 12345678', $stacks);
    }

    public function testStartWithSpecifyDevice()
    {
        $device = new NFCDevice($this->NFCContext);
        $device->open('dummy-2');

        $this->NFCContext->start($device);
        $stacks = $this->stackHandler->getStacks();

        $this->assertContains('Start to listen on device dummy-device [dummy-2]', $stacks);
        $this->assertContains('Touched target: 12345678', $stacks);
        $this->assertContains('Released target: 12345678', $stacks);
    }
}
