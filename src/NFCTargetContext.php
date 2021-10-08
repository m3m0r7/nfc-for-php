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

    public function __construct(NFCContext $context, CData $nfcTargetContext)
    {
        $this->context = $context;
        $this->nfcTargetContext = $nfcTargetContext;

        $this->debug = new NFCDebug($this->context);
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