<?php

declare(strict_types=1);

namespace Tests\Units;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use NFC\Contexts\NullContextProxy;
use NFC\Drivers\Emulator\EmulateDriver;
use NFC\Drivers\Emulator\EmulateKernel;
use NFC\Drivers\LibNFC\Kernel;
use NFC\NFC;
use NFC\NFCContext;
use NFC\NFCEventManager;
use NFC\Util\LoggerStackHandler;
use PHPUnit\Framework\TestCase;

class NFCContextTest extends TestCase
{
    protected ?NFC $NFC = null;
    protected ?NFCContext $NFCContext = null;
    protected LoggerStackHandler $stackHandler;

    public function setUp(): void
    {
        $this->NFC = new NFC(EmulateKernel::class);
        $logger = new Logger(__CLASS__);
        $logger->pushHandler($this->stackHandler = new LoggerStackHandler());
        $this->stackHandler->setFormatter(new LineFormatter('%message%'));

        $this->NFC->setLogger($logger);
        $this->NFCContext = $this->NFC->createContext();
    }

    public function testGetVersion()
    {
        $this->assertSame(
            '0.0.1',
            $this->NFCContext->getVersion()
        );
    }

    public function testListDevices()
    {
        $devices = $this->NFCContext->getDevices();
        $this->assertCount(3, $devices);
        $this->assertSame('emulator-1', $devices[0]->getDeviceName());
        $this->assertSame('emulator-2', $devices[1]->getDeviceName());
        $this->assertSame('emulator-3', $devices[2]->getDeviceName());
    }

    public function testFindDevice()
    {
        $this->assertSame(
            'emulator-1',
            $this->NFCContext->findDeviceName('emulator')->getDeviceName()
        );
    }

    public function testStart()
    {
        $this->NFCContext->start();

        $this->assertSame(
            ['started'],
            $this->stackHandler->getStacks()
        );
    }

    public function testClose()
    {
        $this->NFCContext->close();

        $this->assertSame(
            ['Close the NFC context'],
            $this->stackHandler->getStacks()
        );
    }

    public function testOpen()
    {
        $this->NFCContext->open();

        $this->assertSame(
            ['Open a NFC context'],
            $this->stackHandler->getStacks()
        );
    }
}
