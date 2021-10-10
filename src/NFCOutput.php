<?php
declare(strict_types=1);

namespace NFC;

use FFI\CData;

class NFCOutput
{
    protected NFCContext $context;

    public function __construct(NFCContext $NFCContext)
    {
        $this->context = $NFCContext;
    }

    public function outputNFCTargetContext(NFCTarget $nfcTarget): string
    {
        $string = $this
            ->context
            ->getFFI()
            ->new('char *');

        $this
            ->context
            ->getFFI()
            ->str_nfc_target(
                \FFI::addr($string),
                \FFI::addr($nfcTarget->getNFCTargetContext()),
                true
            );

        try {
            return \FFI::string($string);
        } finally {
            $this
                ->context
                ->getFFI()
                ->nfc_free($string);
        }
    }
}
