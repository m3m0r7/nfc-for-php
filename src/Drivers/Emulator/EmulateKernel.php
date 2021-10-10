<?php

declare(strict_types=1);

namespace NFC\Drivers\Emulator;

use Monolog\Logger;
use NFC\Contexts\NullContextProxy;
use NFC\Drivers\LibNFC\Kernel;
use NFC\NFCContext;
use NFC\NFCEventManager;
use NFC\NFCInterface;
use NFC\Util\LoggerStackHandler;

class EmulateKernel implements NFCInterface
{
    protected NFCContext $context;
    protected Logger $logger;

    public function createContext(?NFCEventManager $eventManager = null): NFCContext
    {
        return $this->context ??= new NFCContext(
            $this,
            new NullContextProxy(),
            new NFCEventManager(),
            EmulateDriver::class,
        );
    }

    public function getContext(): NFCContext
    {
        return $this->context;
    }

    public function setLogger(Logger $logger): NFCInterface
    {
        $this->logger = $logger;
        return $this;
    }

    public function getLogger(): Logger
    {
        if ($this->logger === null) {
            $this->logger = (new Logger(__CLASS__));
            $this->logger->pushHandler(new LoggerStackHandler());
        }

        return $this->logger;
    }
}
