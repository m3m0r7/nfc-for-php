<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC;

use FFI\CData;
use NFC\Attributes\AbstractNFCTargetAttribute;
use NFC\NFCTargetInterface;

abstract class LibNFCAttribute extends AbstractNFCTargetAttribute
{
    protected CData $context;

    public function __construct(NFCTargetInterface $target)
    {
        parent::__construct($target);

        $this->context = $target
            ->getNFCTargetContext()
            ->nti;
    }
}
