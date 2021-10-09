<?php
declare(strict_types=1);

namespace NFC;

use FFI\CData;

class NFCContext
{
    protected \FFI $ffi;
    protected ?CData $context;

    protected int $pollingContinuations = 0xff;
    protected int $pollingInterval = 2;
    protected bool $isOpened = false;

    protected NFCBaudRates $baudRates;
    protected NFCModulationTypes $modulationTypes;
    protected NFCEventManager $eventManager;

    public function __destruct()
    {
        $this->close();
    }

    public function __construct(\FFI $ffi, NFCEventManager $eventManager)
    {
        $this->ffi = $ffi;
        $this->eventManager = $eventManager;
    }

    public function open(): self
    {
        $this->context = $this->ffi->new('nfc_context *');

        $this->ffi->nfc_init(\FFI::addr($this->context));

        $this->baudRates = new NFCBaudRates($this->ffi);
        $this->modulationTypes = new NFCModulationTypes($this->ffi);

        $this->isOpened = true;

        $this->eventManager
            ->dispatchEvent('open', $this);

        return $this;
    }

    public function getFFI(): \FFI
    {
        return $this->ffi;
    }

    public function close(): void
    {
        $this->validateContextOpened();

        $this->ffi->nfc_exit($this->context);
        $this->eventManager->dispatchEvent('close', $this);

        $this->context = null;
        $this->isOpened = false;
    }


    public function getVersion(): string
    {
        $this->validateContextOpened();

        return $this
            ->ffi
            ->nfc_version();
    }

    public function getNFCContext(): CData
    {
        $this->validateContextOpened();

        return $this->context;
    }

    /**
     * @return array<NFCDeviceInfo>
     */
    public function getDevices(int $maxFetchDevices = 128): array
    {
        $this->validateContextOpened();

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
        $this->validateContextOpened();

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

    public function start(array $modulations = [], NFCDevice $device = null): void
    {
        $this->validateContextOpened();

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

        $this->eventManager
            ->dispatchEvent(
                'start',
                $this,
                $device
            );

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
                    $this->eventManager
                        ->dispatchEvent(
                            'touch',
                            $this,
                            $target = new NFCTarget(
                                $this,
                                $device,
                                $nfcTargetContext
                            )
                        );
//
//                    while ($this->ffi->nfc_initiator_target_is_present($device->getDeviceContext(), \FFI::addr($nfcTargetContext)) === 0) {
//                        usleep(250);
//                    }
//
//                    $this->dispatchEvent(
//                        'leave',
//                        $target
//                    );
                } else {
                    $this->eventManager
                        ->dispatchEvent(
                            'missing',
                            $this,
                            $device
                        );
                }
            } catch (\Throwable $e) {
                $this->eventManager
                    ->dispatchEvent(
                        'error',
                        $this,
                        $e
                    );
            }
        }
    }

    public function getBaudRates(): NFCBaudRates
    {
        $this->validateContextOpened();

        return $this->baudRates;
    }

    public function getModulationsTypes(): NFCModulationTypes
    {
        $this->validateContextOpened();

        return $this->modulationTypes;
    }

    private function validateContextOpened()
    {
        // Open context automatically.
        if (!$this->isOpened) {
            $this->open();
        }

        if ($this->context === null) {
            throw new NFCException('Context was closed');
        }
    }

}

