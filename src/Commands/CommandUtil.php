<?php

declare(strict_types=1);

namespace NFC\Commands;

use NFC\NFC;
use NFC\NFCContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
                'D',
                InputOption::VALUE_REQUIRED,
                'Set using driver type [' . implode(', ', array_keys(static::$drivers)) . ']',
                'rcs380'
            )
            ->addOption(
                'lib',
                'L',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Set shared object file path(s). This command find automatically from the specified path(s)',
                ['auto']
            )
            ->addOption(
                'device-name',
                'N',
                InputOption::VALUE_OPTIONAL,
                'Find and using specify device name',
                'auto'
            );
    }

    protected function createNFCContext(InputInterface $input, OutputInterface $output): ?NFCContext
    {
        $driverName = $input->getOption('driver');

        if (!in_array($driverName, array_keys(static::$drivers), true)) {
            $output->writeln("<error>The specified driver name `{$driverName}` is not found</error>");
            return null;
        }

        $kernel = new NFC(static::$drivers[$driverName]);
        return $kernel->createContext();
    }
}