<?php
declare(strict_types=1);

namespace NFC;

use FFI\CData;
use Throwable;

class NFCTargetContext
{
    protected NFCContext $context;
    protected CData $nfcTargetContext;
    protected NFCDebug $debug;
    protected NFCDevice $device;

    public function __construct(NFCContext $context, NFCDevice $device, CData $nfcTargetContext)
    {
        $this->context = $context;
        $this->device = $device;
        $this->nfcTargetContext = $nfcTargetContext;

        $this->debug = new NFCDebug($this->context);
    }

    public function getNFCContext(): NFCContext
    {
        return $this->context;
    }

    public function getNFCDevice(): NFCDevice
    {
        return $this->device;
    }

    public function __toString(): string
    {
        return $this
            ->debug
            ->outputNFCTargetContext(
                $this->nfcTargetContext
            );
    }
}