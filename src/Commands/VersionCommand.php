<?php

declare(strict_types=1);

namespace NFC\Commands;

use NFC\NFC;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VersionCommand extends Command
{
    use CommandUtil;

    protected static $defaultName = 'version';
    protected static $defaultDescription = 'Show driver version';

    protected function configure(): void
    {
        $this
            ->defaultConfigure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $driverName = $input->getOption('driver');

        if (!in_array($driverName, array_keys(static::$drivers), true)) {
            $output->writeln("<error>The specified driver name `{$driverName}` is not found</error>");
            return Command::INVALID;
        }

        $kernel = new NFC(static::$drivers[$driverName]);
        $context = $kernel->createContext();

        $output->writeln("{$context->getVersion()}");

        return Command::SUCCESS;
    }
}
