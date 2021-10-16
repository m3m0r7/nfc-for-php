<?php

declare(strict_types=1);

namespace NFC\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    use CommandUtil;

    protected static $defaultName = 'start';
    protected static $defaultDescription = 'Start to listen connected NFC reader';

    protected function configure(): void
    {
        $this
            ->defaultConfigure()
            ->addUsage('--d=rcs380 -e=/path/to/listen/event.php -dn=SONY')
            ->addOption(
                'device-types',
                'dt',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Set device types [felica, ]',
            )
            ->addOption(
                'event-manager',
                'e',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Set event manager file path [e.g. /path/to/listen/event.php]',
            )
            ->addOption(
                'polling-interval',
                'pi',
                InputOption::VALUE_OPTIONAL,
                'Set polling interval (ms)',
                250
            )
            ->addOption(
                'release-timeout',
                'rt',
                InputOption::VALUE_OPTIONAL,
                'Set NFC tag/card release timeout (ms)',
                30
            )
            ->addOption(
                'wait-presentation-release-interval',
                'ri',
                InputOption::VALUE_OPTIONAL,
                'Set wait presentation release interval (ms)',
                250
            )
            ->addOption(
                'max-retry',
                'rc',
                InputOption::VALUE_OPTIONAL,
                'Set max retry',
                5
            )
            ->addOption(
                'retry-interval',
                'rin',
                InputOption::VALUE_OPTIONAL,
                'Set retry interval (ms)',
                2000
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
