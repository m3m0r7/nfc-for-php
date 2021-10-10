<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\FFIContextProxy;
use NFC\Drivers\LibNFC\Headers\NFCConstants;
use NFC\Drivers\LibNFC\Headers\NFCInternalConstants;
use NFC\Drivers\LibNFC\Headers\NFCTypesConstants;
use NFC\NFCContext;
use NFC\NFCEventManager;
use NFC\NFCException;
use NFC\NFCInterface;

class Kernel implements NFCInterface
{
    protected array $headers = [
        [__DIR__ . '/Headers/cdef/nfc-types.h', [NFCTypesConstants::class]],
        [__DIR__ . '/Headers/cdef/nfc-internal.h', [NFCInternalConstants::class]],
        [__DIR__ . '/Headers/cdef/nfc.h', [NFCConstants::class]],
    ];

    protected array $autoScanLocationsForUnix = [
        '/usr/local/lib',
        '/usr/lib',
    ];

    protected array $autoScanLibraryNames = [
        'libnfc.dylib',
        'libnfc.6.dylib',
        'libnfc.so',
        'libnfc.6.so',
        'libnfc.dll',
        'libnfc.6.dll',
    ];

    protected string $defaultLogFileName = 'nfc-for-php.log';
    protected string $driverClassName = LibNFCDriver::class;
    protected string $FFIContextProxyClassName = FFIContextProxy::class;

    protected ?array $libraryPaths = null;
    protected ?NFCContext $context = null;
    protected ?Logger $logger = null;

    public function __construct($libraryPaths = null)
    {
        if ($libraryPaths === null) {
            $this->libraryPaths = $this->isWindows()
                // Windows does not supported for auto scan locations.
                ? []
                : $this->autoScanLocationsForUnix;

            if (!$this->isWindows()) {
                // Add working directory
                $this->libraryPaths[] = getcwd() . '/libnfc/lib';
            }
        } else {
            $this->libraryPaths = (array) $libraryPaths;
        }
    }

    public function setLogger(Logger $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    public function createContext(?NFCEventManager $eventManager = null): NFCContext
    {
        $libraryPath = null;
        $this->getLogger()->info(
            'Search using libnfc library'
        );

        foreach ($this->libraryPaths as $path) {
            if (is_file($path)) {
                $libraryPath = $path;
                $this->getLogger()->info(
                    "Use library: {$libraryPath}"
                );
                break;
            }
            if (is_dir($path)) {
                foreach ($this->autoScanLibraryNames as $name) {
                    if (is_file($foundFilePath = $path . DIRECTORY_SEPARATOR . $name)) {
                        $libraryPath = $foundFilePath;

                        $this->getLogger()->info(
                            "Use library: {$libraryPath}"
                        );
                        break 2;
                    }
                }
            }
        }

        if ($libraryPath === null) {
            throw new NFCException('Library not found.');
        }

        return $this->context = new NFCContext(
            $this,
            $this->createNFCContextContextProxy($libraryPath),
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
                implode(
                    "\n",
                    array_map(
                        function (array $header) {
                            $headerFile = $header[0] ?? null;
                            $bindDefines = $header[1] ?? null;

                            if (!is_file($headerFile)) {
                                throw new NFCException("Cannot open a header file: {$headerFile}");
                            }

                            $load = file_get_contents($headerFile);
                            if ($bindDefines !== null) {
                                foreach ($bindDefines as $bindListedDefinesClassName) {
                                    $defines = $bindListedDefinesClassName::all();
                                    $load = str_replace(
                                        array_keys($defines),
                                        array_values($defines),
                                        $load
                                    );
                                }
                            }

                            return $load;
                        },
                        $this->headers
                    )
                ),
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

    protected function isWindows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }
}
