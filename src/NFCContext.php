<?php
declare(strict_types=1);

namespace NFC;

use FFI\CData;

class NFCContext
{
    protected \FFI $ffi;
    protected CData $context;

    protected int $pollingContinuations = 0xff;
    protected int $pollingInterval = 2;

    protected NFCBaudRates $baudRates;
    protected NFCModulationTypes $modulationTypes;

    protected array $events = [
        'touch' => [],
        'leave' => [],
    ];

    public function __destruct()
    {
        $this->close();
    }

    public function __construct(\FFI $ffi)
    {
        $this->ffi = $ffi;
        $this->context = $ffi->new('nfc_context *');

        $ffi->nfc_init(\FFI::addr($this->context));

        $this->baudRates = new NFCBaudRates($this->ffi);
        $this->modulationTypes = new NFCModulationTypes($this->ffi);
    }

    public function getFFI(): \FFI
    {
        return $this->ffi;
    }

    public function close(): void
    {
        $this->ffi->nfc_exit($this->context);;
    }


    public function getVersion(): string
    {
        return $this
            ->ffi
            ->nfc_version();
    }

    public function getNFCContext(): CData
    {
        return $this->context;
    }

    /**
     * @return array<NFCDeviceInfo>
     */
    public function getDevices(int $maxFetchDevices = 128): array
    {
        $connectionTargets = $this
            ->ffi
            ->new("nfc_connstring[{$maxFetchDevices}]");

        $this
            ->ffi
            ->nfc_list_devices(
                $this->context,
                $connectionTargets,
                $maxFetchDevices
            );

        $data = [];

        for ($i = 0; $i < $maxFetchDevices; $i++) {
            $data[$i] = \FFI::string($connectionTargets[$i]);
            if ($data[$i] === '') {
                unset($data[$i]);
            }
        }

        return array_values(
            array_filter(
                array_map(
                    function (string $connectionTarget) {
                        try {
                            $nfcDevice = (new NFCDevice($this))
                                ->open($connectionTarget);
                        } catch (NFCDeviceException $e) {
                            return null;
                        }

                        return new NFCDeviceInfo(
                            $connectionTarget,
                            $nfcDevice
                        );
                    },
                    $data,
                )
            )
        );
    }

    public function findDeviceNameContain(string $deviceName): NFCDevice
    {
        $exceptions = [];
        try {
            /**
             * @var NFCDeviceInfo $nfcDevice
             */
            foreach ($this->getDevices() as $nfcDevice) {
                if (strpos($nfcDevice->getDeviceName(), $deviceName) !== false) {
                    return $nfcDevice->getDevice();
                }
            }
        } catch (NFCDeviceException $e) {
            $exceptions[] = $e;
        }

        throw new NFCException(
            implode(
                "\n",
                array_map(
                    fn (string $exception) => (string) $exception,
                    $exceptions
                )
            )
        );
    }

    public function setPollingContinuations(int $pollingContinuations): self
    {
        $this->pollingContinuations = $pollingContinuations;
        return $this;
    }

    public function setPollingInterval(int $interval): self
    {
        $this->pollingInterval = $interval;
        return $this;
    }

    public function addEventListener(string $eventName, callable $callback): self
    {
        if (!isset($this->events[$eventName])) {
            throw new NFCException("Unable add an event `{$eventName}`.");
        }
        $this->events[$eventName][] = $callback;
        return $this;
    }

    protected function dispatchEvent(string $eventName, ...$parameters): void
    {
        if (!isset($this->events[$eventName])) {
            throw new NFCException("Failed to dispatch `{$eventName}`.");
        }

        foreach ($this->events[$eventName] as $callback) {
            $callback(...$parameters);
        }
    }

    public function start(array $modulations = [], NFCDevice $device = null): void
    {
        $modulationsSize = count($modulations);

        $nfcModulations = $this
            ->ffi
            ->new('nfc_modulation[' . $modulationsSize . ']');

        /**
         * @var NFCModulation $modulation
         */
        foreach ($modulations as $index => $modulation) {
            $nfcModulations[$index]->nmt = $modulation->getModulationType();
            $nfcModulations[$index]->nbr = $modulation->getBaudRate();
        }

        if ($device === null) {
            $device = $this->getDevices()[0] ?? null;

            if ($device === null) {
                throw new NFCException('NFC Device not found.');
            }
        }

        while (true) {
            $nfcTargetContext = $this
                ->ffi
                ->new('nfc_target');

            try {
                $pollResult = $this
                    ->ffi
                    ->nfc_initiator_poll_target(
                        $device->getDeviceContext(),
                        $nfcModulations,
                        $modulationsSize,
                        $this->pollingContinuations,
                        $this->pollingInterval,
                        \FFI::addr($nfcTargetContext),
                    );

                if ($pollResult > 0) {
                    $this->dispatchEvent(
                        'touch',
                        $target = new NFCTarget(
                            $this,
                            $device,
                            $nfcTargetContext
                        )
                    );

                    while ($this->ffi->nfc_initiator_target_is_present($device->getDeviceContext(), \FFI::addr($nfcTargetContext)) === 0) {
                        usleep(250);
                    }

                    $this->dispatchEvent(
                        'leave',
                        $target
                    );
                }
            } catch (\Exception $e) {
                echo $e . "\n";
            }
        }
    }

    public function getBaudRates(): NFCBaudRates
    {
        return $this->baudRates;
    }

    public function getModulationsTypes(): NFCModulationTypes
    {
        return $this->modulationTypes;
    }

}

