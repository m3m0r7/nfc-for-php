<?php
declare(strict_types=1);

namespace NFC\Drivers\LibNFC;

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

    protected ?array $libraryPaths = null;
    protected ?NFCContext $context = null;

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

    public function createContext(?NFCEventManager $eventManager = null): NFCContext
    {
        $libraryPath = null;
        foreach ($this->libraryPaths as $path) {
            if (is_file($path)) {
                $libraryPath = $path;
                break;
            }
            if (is_dir($path)) {
                foreach ($this->autoScanLibraryNames as $name) {
                    if (is_file($foundFilePath = $path . DIRECTORY_SEPARATOR . $name)) {
                        $libraryPath = $foundFilePath;
                        break 2;
                    }
                }
            }
        }

        return $this->context = new NFCContext(
            $this,
            new FFIContextProxy(
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
            ),
            $eventManager ?? new NFCEventManager(),
            LibNFC::class,
        );
    }

    public function getContext(): NFCContext
    {
        $this->validateContextOpened();

        return $this->context;
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
