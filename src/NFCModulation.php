<?php
declare(strict_types=1);

namespace NFC;

use Throwable;

class NFCModulation
{
    protected int $modulationType;
    protected int $baudRate;

    public function __construct(int $modulationType, int $baudRate)
    {
        $this->modulationType = $modulationType;
        $this->baudRate = $baudRate;
    }

    public function getModulationType(): int
    {
        return $this->modulationType;
    }

    public function getBaudRate(): int
    {
        return $this->baudRate;
    }
}