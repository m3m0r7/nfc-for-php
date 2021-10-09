<?php
declare(strict_types=1);

namespace NFC;

use FFI\CData;

class NFC
{
    protected array $definitions = [
        __DIR__ . '/definitions/nfc-types.cdef',
        __DIR__ . '/definitions/nfc-routines.cdef',
    ];
    protected string $libraryPath;
    protected ?NFCContext $context = null;

    public function __construct(string $libraryPath)
    {
        $this->libraryPath = $libraryPath;
    }

    public function createContext(?NFCEventManager $eventManager = null): NFCContext
    {
        return $this->context = new NFCContext(
            \FFI::cdef(
                implode(
                    "\n",
                    array_map(
                        static fn (string $definition) => file_get_contents($definition),
                        $this->definitions
                    )
                ),
                $this->libraryPath,
            ),
            $eventManager ?? new NFCEventManager(),
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
}
