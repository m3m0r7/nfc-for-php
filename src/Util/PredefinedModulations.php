<?php

declare(strict_types=1);

namespace NFC\Util;

use NFC\Collections\NFCModulationsInterface;
use NFC\NFCBaudRatesInterface;
use NFC\NFCContext;
use NFC\NFCModulationTypesInterface;

class PredefinedModulations
{
    protected NFCContext $context;
    protected NFCModulationTypesInterface $modulationTypes;
    protected NFCBaudRatesInterface $baudRates;
    protected string $collectionClassName;

    public function __construct(string $collectionClassName, NFCContext $NFCContext)
    {
        $this->collectionClassName = $collectionClassName;
        $this->context = $NFCContext;
        $this->modulationTypes = $this->context->getModulationsTypes();
        $this->baudRates = $this->context->getBaudRates();
    }

    public function all(): NFCModulationsInterface
    {
        return (new ($this->collectionClassName)())
            ->merge($this->ISO14443A())
            ->merge($this->ISO14443B())
            ->merge($this->FeliCa())
            ->merge($this->ISO14443BICLASS());
    }

    public function ISO14443A(): NFCModulationsInterface
    {
        return new ($this->collectionClassName)(
            [
            new \NFC\NFCModulation($this->modulationTypes->NMT_ISO14443A, $this->baudRates->NBR_106),
            ]
        );
    }


    public function ISO14443B(): NFCModulationsInterface
    {
        return new ($this->collectionClassName)(
            [
            new \NFC\NFCModulation($this->modulationTypes->NMT_ISO14443B, $this->baudRates->NBR_106),
            ]
        );
    }

    public function ISO14443BICLASS(): NFCModulationsInterface
    {
        return new ($this->collectionClassName)(
            [
            new \NFC\NFCModulation($this->modulationTypes->NMT_ISO14443BICLASS, $this->baudRates->NBR_106),
            ]
        );
    }

    public function JEWEL(): NFCModulationsInterface
    {
        return new ($this->collectionClassName)(
            [
            new \NFC\NFCModulation($this->modulationTypes->NMT_JEWEL, $this->baudRates->NBR_106),
            ]
        );
    }

    public function FeliCa(): NFCModulationsInterface
    {
        return new ($this->collectionClassName)(
            [
            new \NFC\NFCModulation($this->modulationTypes->NMT_FELICA, $this->baudRates->NBR_212),
            new \NFC\NFCModulation($this->modulationTypes->NMT_FELICA, $this->baudRates->NBR_424),
            ]
        );
    }
}
