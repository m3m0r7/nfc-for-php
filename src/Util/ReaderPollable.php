<?php

declare(strict_types=1);

namespace NFC\Util;

trait ReaderPollable
{
    protected int $pollingContinuations = -1;
    protected int $pollingInterval = 2;
    protected int $pollCount = 0;

    public function setPollingContinuations(int $pollingContinuations): self
    {
        $this->pollingContinuations = $pollingContinuations;
        return $this;
    }

    public function setPollingInterval(int $interval): self
    {
        $this->pollingInterval = $interval;
        return $this;
    }

    public function getPollingContinuations(): int
    {
        return $this->pollingContinuations;
    }

    public function getPollingInterval(): int
    {
        return $this->pollingInterval;
    }

    protected function hasNext(): bool
    {
        if ($this->pollingContinuations < 0) {
            return true;
        }
        return $this->pollCount <= $this->pollingContinuations;
    }
}
