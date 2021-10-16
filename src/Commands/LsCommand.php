<?php

declare(strict_types=1);

namespace NFC\Commands;

use NFC\NFCDeviceInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
        $context = $this->createNFCContext($input, $output);

        if ($context === null) {
            return Command::INVALID;
        }

        $deviceName = $input->getOption('device-name');

        if (strtolower($deviceName) === 'auto') {
            $deviceName = null;
        }

        $counter = 0;
        /**
         * @var NFCDeviceInfo $device
         */
        foreach ($context->getDevices() as $device) {
            if ($deviceName !== null && strpos($device->getDevice()->getDeviceName(), $deviceName) === false) {
                continue;
            }
            $connectionTarget = explode(':', $device->getConnectionTarget());
            $output->writeln(sprintf(
                'Bus: %03s Device %03s: %s',
                $connectionTarget[1],
                $connectionTarget[2],
                $device->getDevice()->getDeviceName()
            ));

            $counter++;
        }

        if ($counter === 0) {
            $output->writeln("Device not found");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
