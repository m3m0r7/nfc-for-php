<?php

declare(strict_types=1);

namespace NFC;

interface NFCInterface
{
    public function createContext(?NFCEventManager $eventManager = null): NFCContext;
    public function getContext(): NFCContext;
}
