<?php

declare(strict_types=1);

namespace NFC;

use Monolog\Logger;

interface NFCInterface
{
    public function createContext(?NFCEventManager $eventManager = null): NFCContext;
    public function getContext(): NFCContext;
    public function setLogger(Logger $logger): NFCInterface;
    public function getLogger(): Logger;
}
