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
        $context = $this->createNFCContext($input, $output);

        if ($context === null) {
            return Command::INVALID;
        }

        $output->writeln("{$context->getVersion()}");

        return Command::SUCCESS;
    }
}
