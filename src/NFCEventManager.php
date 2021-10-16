<?php

declare(strict_types=1);

namespace NFC;

use Illuminate\Support\Collection;

class NFCEventManager
{
    public const EVENT_OPEN = 'open';
    public const EVENT_CLOSE = 'close';
    public const EVENT_START = 'start';
    public const EVENT_TOUCH = 'touch';
    public const EVENT_RELEASE = 'release';
    public const EVENT_MISSING = 'missing';
    public const EVENT_ERROR = 'error';

    protected array $accepted = [
        self::EVENT_OPEN,
        self::EVENT_CLOSE,
        self::EVENT_START,
        self::EVENT_TOUCH,
        self::EVENT_RELEASE,
        self::EVENT_MISSING,
        self::EVENT_ERROR,
    ];

    protected array $items = [];

    public function listen(string $eventName, callable $callback): self
    {
        if (!in_array($eventName, $this->accepted, true)) {
            throw new NFCException("Unable add an event `{$eventName}`.");
        }

        if (!isset($this->items[$eventName])) {
            $this->items[$eventName] = [];
        }

        $this->items[$eventName][] = $callback;
        return $this;
    }

    public function getEvents(): array
    {
        return $this->items;
    }

    public function dispatchEvent(string $eventName, ...$parameters): void
    {
        if (!in_array($eventName, $this->accepted, true)) {
            throw new NFCException("Unable add an event `{$eventName}`.");
        }

        foreach ($this->items[$eventName] ?? [] as $callback) {
            $callback(...$parameters);
        }
    }

    public function merge(NFCEventManager $eventManager): self
    {
        foreach ($eventManager->getEvents() as $eventName => $values) {
            foreach ($values as $callback) {
                $this->listen($eventName, $callback);
            }
        }
        return $this;
    }
}
