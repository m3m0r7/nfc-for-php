<?php

declare(strict_types=1);

namespace NFC\Commands;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use NFC\NFC;
use NFC\NFCContext;
use NFC\NFCEventManager;
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

        $eventManager = new NFCEventManager();

        foreach ($input->getOption('event-manager') ?? [] as $file) {
            if (!is_file($file)) {
                $output->writeln("<error>The specified event manager `{$file}` is not found</error>");
                return null;
            }
            $loadedFile = include $file;

            if (!($loadedFile instanceof NFCEventManager)) {
                $output->writeln("<error>The specified event manager `{$file}` is invalid</error>");
                return null;
            }
            $eventManager->merge($loadedFile);
        }


        $verbose = (int) $input->getOption('verbose');

        $kernel = new NFC(static::$drivers[$driverName]);
        $logger = new Logger($driverName);
        $handler = new StreamHandler(
            'php://stdout',
            $verbose <= 1
                ? Logger::WARNING
                : (
                    $verbose === 2
                        ? Logger::INFO
                        : Logger::DEBUG
                ),
        );

        $handler->setFormatter(
            new LineFormatter(
                "[%datetime%] %level_name%: %message%\n",
                'Y-m-d H:i:s',
                true
            )
        );

        $logger
            ->pushHandler($handler);

        $kernel->setLogger($logger);

        return $kernel->createContext($eventManager);
    }
}
