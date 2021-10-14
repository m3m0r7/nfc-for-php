<?php

declare(strict_types=1);

namespace Tests\Mock;

use NFC\Collections\NFCModulationsInterface;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NFCTargetContextProxy;
use NFC\Drivers\LibNFC\LibNFCDriver;
use NFC\NFCDeviceInterface;
use NFC\NFCTargetInterface;

class MockedLibNFCDriver extends LibNFCDriver
{
    protected string $NFCTargetClassName = MockedNFCTarget::class;

    public function poll(NFCDeviceInterface $device, NFCModulationsInterface $modulations): ?ContextProxyInterface
    {
        $target = parent::poll($device, $modulations);
        $target->nm->nmt = 7;
        $target->nm->nbr = 2;

        return new NFCTargetContextProxy($target->getContext());
    }

    public function isPresent(NFCDeviceInterface $device, NFCTargetInterface $target): bool
    {
        return true;
    }

    protected function hasNext(): bool
    {
        return false;
    }
}