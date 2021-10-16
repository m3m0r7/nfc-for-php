<?php

declare(strict_types=1);

namespace NFC\Util;

trait ReaderRetryable
{
    protected int $maxRetry = 5;
    protected int $retryInterval = 2000;

    public function setMaxRetry(int $ms): self
    {
        $this->maxRetry = $ms;
        return $this;
    }

    public function setRetryInterval(int $ms): self
    {
        $this->retryInterval = $ms;
        return $this;
    }
}
