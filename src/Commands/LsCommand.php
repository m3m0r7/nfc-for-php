<?php

declare(strict_types=1);

namespace NFC\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LsCommand extends Command
{
    use CommandUtil;

    protected static $defaultName = 'ls';
    protected static $defaultDescription = 'List devices';

    protected function configure(): void
    {
        $this
            ->defaultConfigure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
