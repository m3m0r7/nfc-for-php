<?php
declare(strict_types=1);

namespace NFC;

class NFCEventManager
{
    public const EVENT_OPEN = 'open';
    public const EVENT_CLOSE = 'close';
    public const EVENT_START = 'start';
    public const EVENT_TOUCH = 'touch';
    public const EVENT_LEAVE = 'leave';
    public const EVENT_MISSING = 'missing';
    public const EVENT_ERROR = 'error';

    protected array $events = [
        self::EVENT_OPEN => [],
        self::EVENT_CLOSE => [],
        self::EVENT_START => [],
        self::EVENT_TOUCH => [],
        self::EVENT_LEAVE => [],
        self::EVENT_MISSING => [],
        self::EVENT_ERROR => [],
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