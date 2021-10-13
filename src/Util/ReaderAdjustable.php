<?php

declare(strict_types=1);

namespace NFC\Util;

trait ReaderAdjustable
{
    // second
    protected int $continuousTouchAdjustmentExpires = 10;
    protected bool $enableContinuousTouchAdjustment = true;

    public function enableContinuousTouchAdjustment(bool $which): self
    {
        $this->enableContinuousTouchAdjustment = $which;
        return $this;
    }

    public function setContinuousTouchAdjustmentExpires(int $second): self
    {
        $this->continuousTouchAdjustmentExpires = $second;
        return $this;
    }
}
