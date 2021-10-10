<?php
declare(strict_types=1);

namespace NFC\Collections;

use FFI\CData;
use Illuminate\Support\Collection;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\FFIContextProxy;
use NFC\NFCModulation;
use NFC\NFCTarget;

class NFCModulations extends Collection
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

        $nfcModulations = $context
            ->new('nfc_modulation[' . count($this->items) . ']');

        /**
         * @var NFCModulation $modulation
         */
        foreach ($this->items as $index => $modulation) {
            $nfcModulations[$index]->nmt = $modulation->getModulationType();
            $nfcModulations[$index]->nbr = $modulation->getBaudRate();
        }

        return $this->cdataStructure = $nfcModulations;
    }
}
