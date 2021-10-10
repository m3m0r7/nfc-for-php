<?php

declare(strict_types=1);

namespace NFC\Util;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class LoggerStackHandler extends AbstractProcessingHandler
{
    protected $level = Logger::INFO;

    protected array $stacks = [];

    protected function write(array $record): void
    {
        $this->stacks[] = $record['formatted'];
    }

    public function clear(): void
    {
        $this->stacks = [];
    }

    public function getStacks(): array
    {
        return $this->stacks;
    }
}
