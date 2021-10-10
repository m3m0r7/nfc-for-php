<?php

declare(strict_types=1);

namespace NFC;

use Monolog\Logger;

/**
 * @method NFCContext createContext(?NFCEventManager $eventManager = null)
 * @method NFCContext getContext()
 * @method NFCInterface setLogger(Logger $logger)
 * @method Logger getLogger()
 */
class NFC
{
    protected NFCInterface $NFC;

    public function __construct(string $NFCKernelClassName = \NFC\Drivers\LibNFC\Kernel::class, ...$parameters)
    {
        $this->NFC = new $NFCKernelClassName(...$parameters);
    }

    public function __call($name, $parameters)
    {
        return $this->NFC->{$name}(...$parameters);
    }
}
