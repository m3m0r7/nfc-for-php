<?php

declare(strict_types=1);

namespace NFC\Util;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use NFC\Contexts\ContextProxyInterface;
use NFC\NFCContext;
use NFC\NFCEventManager;
use NFC\NFCException;
use NFC\NFCInterface;

abstract class SimpleKernel implements NFCInterface
{
    use LibraryFindable;

    protected array $autoScanLocationsForUnix = [
        '/usr/local/lib',
        '/usr/lib',
    ];

    protected string $defaultLogFileName = 'nfc-for-php.log';
    protected ?NFCContext $context = null;
    protected ?Logger $logger = null;

    protected string $driverClassName;
    protected string $FFIContextProxyClassName;

    public function __construct($libraryPaths = null)
    {
        $this->setLibraryPaths($libraryPaths);
    }

    public function setLogger(Logger $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    public function createContext(?NFCEventManager $eventManager = null): NFCContext
    {
        return $this->context = new NFCContext(
            $this,
            $this->createNFCContextContextProxy(
                $this->selectLibraryPath()
            ),
            $eventManager ?? new NFCEventManager(),
            $this->driverClassName,
        );
    }

    public function getLogger(): Logger
    {
        if ($this->logger === null) {
            $this->logger = new Logger(__CLASS__);
            $handler = new StreamHandler(
                getcwd() . "/{$this->defaultLogFileName}",
                Logger::INFO,
            );

            $handler->setFormatter(
                new LineFormatter(
                    "[%datetime%] %level_name%: %message%\n",
                    'Y-m-d H:i:s',
                    true
                )
            );

            $this->logger
                ->pushHandler($handler);
        }
        return $this->logger;
    }

    public function getContext(): NFCContext
    {
        $this->validateContextOpened();

        return $this->context;
    }

    protected function createNFCContextContextProxy(string $libraryPath): ContextProxyInterface
    {
        return new ($this->FFIContextProxyClassName)(
            \FFI::cdef(
                $this->createCDefHeader(),
                $libraryPath,
            )
        );
    }

    protected function validateContextOpened(): void
    {
        if ($this->context === null) {
            throw new NFCException(
                "Context not opened. Please run `NFC::createContext` before."
            );
        }
    }
}