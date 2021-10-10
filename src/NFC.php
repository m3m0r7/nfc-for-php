<?php

declare(strict_types=1);

namespace NFC;

/**
 * @mixin NFCInterface
 */
class NFC
{
    protected NFCInterface $nfc;

    public function __construct(string $NFCDriverClassName = \NFC\Drivers\LibNFC\Kernel::class, ...$parameters)
    {
        $this->nfc = new $NFCDriverClassName(...$parameters);
    }

    public function __call($name, $parameters)
    {
        return $this->nfc->{$name}(...$parameters);
    }

    public function createContext(?NFCEventManager $eventManager = null, string $driverClassName = null): NFCContext
    {
        return $this->nfc->createContext($eventManager, $driverClassName);
    }

    public function getContext(): NFCContext
    {
        return $this->nfc->getContext();
    }
}
