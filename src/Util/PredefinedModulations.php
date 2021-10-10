<?php

declare(strict_types=1);

namespace NFC\Util;

use NFC\Collections\NFCModulations;
use NFC\NFCBaudRatesInterface;
use NFC\NFCContext;
use NFC\NFCModulationTypesInterface;

class PredefinedModulations
{
    protected NFCContext $context;
    protected NFCModulationTypesInterface $modulationTypes;
    protected NFCBaudRatesInterface $baudRates;

    public function __construct(NFCContext $NFCContext)
    {
        $this->context = $NFCContext;
        $this->modulationTypes = $this->context->getModulationsTypes();
        $this->baudRates = $this->context->getBaudRates();
    }

    public function all(): NFCModulations
    {
        return (new NFCModulations())
            ->merge($this->ISO14443A())
            ->merge($this->ISO14443B())
            ->merge($this->FeliCa())
            ->merge($this->ISO14443BICLASS());
    }

    public function ISO14443A(): NFCModulations
    {
        return new NFCModulations([
            new \NFC\NFCModulation($this->modulationTypes->NMT_ISO14443A, $this->baudRates->NBR_106),
        ]);
    }


    public function ISO14443B(): NFCModulations
    {
        return new NFCModulations([
            new \NFC\NFCModulation($this->modulationTypes->NMT_ISO14443B, $this->baudRates->NBR_106),
        ]);
    }

    public function ISO14443BICLASS(): NFCModulations
    {
        return new NFCModulations([
            new \NFC\NFCModulation($this->modulationTypes->NMT_ISO14443BICLASS, $this->baudRates->NBR_106),
        ]);
    }

    public function JEWEL(): NFCModulations
    {
        return new NFCModulations([
            new \NFC\NFCModulation($this->modulationTypes->NMT_JEWEL, $this->baudRates->NBR_106),
        ]);
    }

    public function FeliCa(): NFCModulations
    {
        return new NFCModulations([
            new \NFC\NFCModulation($this->modulationTypes->NMT_FELICA, $this->baudRates->NBR_212),
            new \NFC\NFCModulation($this->modulationTypes->NMT_FELICA, $this->baudRates->NBR_424),
        ]);
    }
}
