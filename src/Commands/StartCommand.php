<?php

declare(strict_types=1);

namespace NFC\Commands;

use NFC\Drivers\LibNFC\NFCModulations;
use NFC\Drivers\RCS380\RCS380Driver;
use NFC\NFCDeviceNotFoundException;
use NFC\NFCEventManager;
use NFC\NFCModulation;
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
            ->addUsage('-D=rcs380 -E=/path/to/listen/event.php -N=SONY')
            ->addOption(
                'device-type',
                'T',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Set device types [FeliCa]',
                ['FeliCa']
            )
            ->addOption(
                'event-manager',
                'E',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Set event manager file path [e.g. /path/to/listen/event.php]',
            )
            ->addOption(
                'polling-interval',
                'P',
                InputOption::VALUE_OPTIONAL,
                'Set polling interval (ms)',
                250
            )
            ->addOption(
                'release-timeout',
                'R',
                InputOption::VALUE_OPTIONAL,
                'Set NFC tag/card release timeout (ms)',
                30
            )
            ->addOption(
                'wait-presentation-release-interval',
                'W',
                InputOption::VALUE_OPTIONAL,
                'Set wait presentation release interval (ms)',
                250
            )
            ->addOption(
                'max-retry',
                'M',
                InputOption::VALUE_OPTIONAL,
                'Set max retry to send a command',
                5
            )
            ->addOption(
                'retry-interval',
                'C',
                InputOption::VALUE_OPTIONAL,
                'Set retry interval (ms) to send a command',
                2000
            )
            ->addOption(
                'enable-touch-adjustment',
                'A',
                InputOption::VALUE_OPTIONAL,
                'Set touch adjustment',
                '1'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = $this->createNFCContext($input, $output);

        if ($context === null) {
            return Command::INVALID;
        }

        $deviceName = $input->getOption('device-name');
        $device =  null;

        if (strtolower($deviceName) !== 'auto') {
            $deviceName = (string) $deviceName;
            try {
                $device = $context
                    ->findDeviceName(
                        $deviceName
                    );
            } catch (NFCDeviceNotFoundException $e) {
                $output->writeln("The specified device `{$deviceName}` not found");
                return Command::INVALID;
            }
        } else {
            $deviceName = null;
        }

        $modulations = new NFCModulations();
        $modulationTypes = $context->getModulationsTypes();
        $baudRates = $context->getBaudRates();

        foreach ($input->getOption('device-type') as $deviceType) {
            $loweredDeviceType = strtolower($deviceType);
            if ($loweredDeviceType === 'felica') {
                $modulations->add(new NFCModulation($modulationTypes->NMT_FELICA, $baudRates->NBR_212));
                $modulations->add(new NFCModulation($modulationTypes->NMT_FELICA, $baudRates->NBR_424));
            }
        }

        $context
            ->enableContinuousTouchAdjustment((int) $input->getOption('enable-touch-adjustment') === 1);

        $context
            ->setWaitPresentationReleaseInterval((int) $input->getOption('wait-presentation-release-interval'));

        $context
            ->setPollingInterval((int) $input->getOption('polling-interval'));

        $context
            ->setWaitDidNotReleaseTimeout((int) $input->getOption('release-timeout'));

        if ($context->getDriver() instanceof RCS380Driver) {
            $context
                ->setMaxRetry((int) $input->getOption('max-retry'));
            $context
                ->setRetryInterval((int) $input->getOption('retry-interval'));
        }

        $context
            ->start($device, $modulations);

        return Command::SUCCESS;
    }
}
