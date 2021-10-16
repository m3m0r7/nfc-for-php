<?php

declare(strict_types=1);

namespace NFC\Util;

trait ReaderReleasable
{
    protected int $waitPresentationReleaseInterval = 250;
    protected int $waitDidNotReleaseTimeout = 30;

    public function setWaitPresentationReleaseInterval(int $ms): self
    {
        $this->waitPresentationReleaseInterval = $ms;
        return $this;
    }

    public function setWaitDidNotReleaseTimeout(int $s): self
    {
        $this->waitDidNotReleaseTimeout = $s;
        return $this;
    }
}
