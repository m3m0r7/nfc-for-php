<?php
declare(strict_types=1);

namespace NFC;

class NFCEventManager
{
    protected array $events = [
        'open' => [],
        'close' => [],
        'start' => [],
        'touch' => [],
        'leave' => [],
        'missing' => [],
        'error' => [],
    ];

    public function listen(string $eventName, callable $callback): self
    {
        if (!isset($this->events[$eventName])) {
            throw new NFCException("Unable add an event `{$eventName}`.");
        }
        $this->events[$eventName][] = $callback;
        return $this;
    }


    public function dispatchEvent(string $eventName, ...$parameters): void
    {
        if (!isset($this->events[$eventName])) {
            throw new NFCException("Failed to dispatch `{$eventName}`.");
        }

        foreach ($this->events[$eventName] as $callback) {
            $callback(...$parameters);
        }
    }

}