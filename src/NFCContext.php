<?php
declare(strict_types=1);

namespace NFC;

use FFI\CData;
use NFC\Collections\NFCModulations;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\FFIContextProxy;
use NFC\Contexts\NFCTargetContextProxy;
use NFC\Headers\NFCConstants;
use NFC\Headers\NFCInternalConstants;

class NFCContext
{
    protected FFIContextProxy $ffi;
    protected ?CData $context;

    protected int $pollingContinuations = 0xff;
    protected int $pollingInterval = 2;
    protected int $waitPresentationReleaseInterval = 250;
    protected int $maxFetchDevices = NFCInternalConstants::DEVICE_PORT_LENGTH;
    protected bool $isOpened = false;
    protected bool $enableContinuousTouchAdjustment = true;

    // second
    protected int $continuousTouchAdjustmentExpires = 10;

    protected NFCBaudRates $baudRates;
    protected NFCModulationTypes $modulationTypes;
    protected NFCEventManager $eventManager;
    protected NFCOutput $output;

    public function __destruct()
    {
        $this->close();
    }

    public function __construct(ContextProxyInterface $ffi, NFCEventManager $eventManager)
    {
        /**
         * @var FFIContextProxy $ffi
         */
        $this->ffi = $ffi;
        $this->eventManager = $eventManager;
    }

    public function open(): self
    {
        $this->context = $this->ffi->new('nfc_context *');

        $this->ffi->nfc_init(\FFI::addr($this->context));

        $this->output = new NFCOutput($this);
        $this->baudRates = new NFCBaudRates($this->ffi);
        $this->modulationTypes = new NFCModulationTypes($this->ffi);

        $this->isOpened = true;

        $this->eventManager
            ->dispatchEvent(
                NFCEventManager::EVENT_OPEN,
                $this
            );

        return $this;
    }

    public function getFFI(): ContextProxyInterface
    {
        return $this->ffi;
    }

    public function close(): void
    {
        $this->validateContextOpened();

        $this->ffi->nfc_exit($this->context);
        $this->eventManager
            ->dispatchEvent(
                NFCEventManager::EVENT_CLOSE,
                $this
            );

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

    public function enableContinuousTouchAdjustment(bool $which): self
    {
        $this->enableContinuousTouchAdjustment = $which;
        return $this;
    }

    public function setContinuousTouchAdjustmentExpires(int $second): self
    {
        $this->continuousTouchAdjustmentExpires = $second;
        return $this;
    }

    public function setWaitPresentationReleaseInterval(int $ms): self
    {
        $this->waitPresentationReleaseInterval = $ms;
        return $this;
    }

    public function setMaxFetchDevices(int $devices): self
    {
        $this->maxFetchDevices = $devices;
        return $this;
    }

    public function getNFCContext(): CData
    {
        $this->validateContextOpened();

        return $this->context;
    }

    /**
     * @return array<NFCDeviceInfo>
     */
    public function getDevices(bool $includeCannotOpenDevices = false): array
    {
        $this->validateContextOpened();

        $connectionTargets = $this
            ->ffi
            ->new("nfc_connstring[{$this->maxFetchDevices}]");

        $this
            ->ffi
            ->nfc_list_devices(
                $this->context,
                $connectionTargets,
                $this->maxFetchDevices
            );

        $data = [];

        for ($i = 0; $i < $this->maxFetchDevices; $i++) {
            $data[$i] = \FFI::string($connectionTargets[$i]);
            if ($data[$i] === '') {
                unset($data[$i]);
            }
        }

        return array_values(
            array_filter(
                array_map(
                    function (string $connectionTarget) use ($includeCannotOpenDevices) {
                        try {
                            $nfcDevice = (new NFCDevice($this))
                                ->open($connectionTarget, $includeCannotOpenDevices);
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

        if (count($exceptions) === 0) {
            throw new NFCException('Unable to find available device.');
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

    public function start(NFCDevice $device = null, NFCModulations $modulations = null): void
    {
        $this->validateContextOpened();

        if ($device === null) {
            $device = $this->getDevices()[0] ?? null;

            if ($device === null) {
                throw new NFCException('NFC Device not found.');
            }
        }

        $this->eventManager
            ->dispatchEvent(
                NFCEventManager::EVENT_START,
                $this,
                $device
            );

        $touched = null;

        while (true) {
            switch ($device->getLastErrorCode()) {
                case NFCConstants::NFC_ETIMEOUT: // Device not configured (This is shown when you did plug-out the NFC device)
                case NFCConstants::NFC_ERFTRANS:
                case NFCConstants::NFC_EMFCAUTHFAIL:
                case NFCConstants::NFC_ESOFT:
                case NFCConstants::NFC_ECHIP:
                case NFCConstants::NFC_EOPABORTED:
                    throw new NFCException(
                        "An error occurred: {$device->getLastErrorName()}({$device->getLastErrorCode()})"
                    );
                    break;
            }

            try {

                if (($nfcTargetContext = $this->poll($device, $modulations)) === null) {
                    $this->eventManager
                        ->dispatchEvent(
                            NFCEventManager::EVENT_MISSING,
                            $this,
                            $device
                        );
                    continue;
                }

                $target = new NFCTarget(
                    $this,
                    $device,
                    $nfcTargetContext
                );

                $info = [
                    'class' => get_class(
                        $target
                            ->getAttributeAccessor()
                    ),
                    'id' => $target
                        ->getAttributeAccessor()
                        ->getID()
                ];

                $isTouchedMoment = false;

                if ($touched !== null && $info['id'] === $touched['id']) {
                    if ($touched['expires'] > time()) {
                        $isTouchedMoment = true;
                    }
                }

                if ($isTouchedMoment) {
                    continue;
                }

                $this->eventManager
                    ->dispatchEvent(
                        NFCEventManager::EVENT_TOUCH,
                        $this,
                        $target
                    );

                if ($this->enableContinuousTouchAdjustment) {
                    $touched = $info + [
                        'expires' => time() + $this->continuousTouchAdjustmentExpires,
                    ];
                }

                try {
                    while ($this->isPresent($device, $target)) {
                        usleep($this->waitPresentationReleaseInterval);
                    }

                    $this->eventManager
                        ->dispatchEvent(
                            NFCEventManager::EVENT_RELEASE,
                            $this,
                            $target
                        );
                } catch (\Throwable $e) {
                    throw $e;
                }
            } catch (\Throwable $e) {
                $this->eventManager
                    ->dispatchEvent(
                        NFCEventManager::EVENT_ERROR,
                        $this,
                        $e
                    );
            }
        }
    }

    public function isPresent(NFCDevice $device, NFCTarget $target): bool
    {
        $isPresent = $this->ffi
            ->nfc_initiator_target_is_present(
                $device->getDeviceContext(),
                \FFI::addr($target->getNFCTargetContext()->getContext()
                )
            ) === 0;
        
        if ($device->getLastErrorCode() !== NFCConstants::NFC_ETGRELEASED) {
            throw new NFCException(
                "An error occurred: {$device->getLastErrorName()}({$device->getLastErrorCode()})"
            );
        }

        return $isPresent;
    }

    public function poll(NFCDevice $device, NFCModulations $modulations): ?ContextProxyInterface
    {
        $nfcTargetContext = $this
            ->ffi
            ->new('nfc_target');

        $result = $this
            ->ffi
            ->nfc_initiator_poll_target(
                $device->getDeviceContext(),
                $modulations->toCDataStructure($this->ffi),
                count($modulations),
                $this->pollingContinuations,
                $this->pollingInterval,
                \FFI::addr($nfcTargetContext),
            );

        if ($result <= 0) {
            return null;
        }

        return new NFCTargetContextProxy($nfcTargetContext);
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

    public function getOutput(): NFCOutput
    {
        return $this->output;
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

