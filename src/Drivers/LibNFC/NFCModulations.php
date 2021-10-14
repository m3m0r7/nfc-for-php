<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC;

use FFI\CData;
use Illuminate\Support\Collection;
use NFC\Collections\NFCModulationsInterface;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\FFIContextProxy;
use NFC\NFCModulation;

class NFCModulations extends Collection implements NFCModulationsInterface
{
    protected ?CData $cdataStructure = null;

    public function toCDataStructure(ContextProxyInterface $context): CData
    {
        /**
         * @var FFIContextProxy $context
         */

        if ($this->cdataStructure) {
            return $this->cdataStructure;
        }

        $NFCModulations = $context
            ->new('nfc_modulation[' . count($this->items) . ']');

        /**
         * @var NFCModulation $modulation
         */
        foreach ($this->items as $index => $modulation) {
            $NFCModulations[$index]->nmt = $modulation->getModulationType();
            $NFCModulations[$index]->nbr = $modulation->getBaudRate();
        }

        return $this->cdataStructure = $NFCModulations;
    }
}
