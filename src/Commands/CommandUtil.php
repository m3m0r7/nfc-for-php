<?php

declare(strict_types=1);

namespace NFC\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

trait CommandUtil
{
    protected static $drivers = [
        'libnfc' => \NFC\Drivers\LibNFC\Kernel::class,
        'rcs380' => \NFC\Drivers\RCS380\Kernel::class,
    ];

    protected function defaultConfigure(): self
    {
        return $this
//            ->addUsage('start --d=rcs380 -e=/path/to/listen/event.php -dn=SONY')
//            ->addUsage('list -d=rcs380')
//            ->addUsage('list -d=rcs380 -dn=SONY')
//            ->addUsage('version -d=rcs380')
            ->addOption(
                'driver',
                'd',
                InputOption::VALUE_REQUIRED,
                'Set using driver type [' . implode(', ', array_keys(static::$drivers)) . ']',
                'rcs380'
            )
            ->addOption(
                'lib',
                'l',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Set shared object file path(s). This command find automatically from the specified path(s)',
                ['auto']
            )
            ->addOption(
                'device-name',
                'dn',
                InputOption::VALUE_OPTIONAL,
                'Find and using specify device name',
                'auto'
            );
    }
}