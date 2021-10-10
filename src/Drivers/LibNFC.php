<?php
declare(strict_types=1);

namespace NFC\Drivers;

use FFI\CData;
use NFC\Collections\NFCModulations;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NFCTargetContextProxy;
use NFC\Headers\NFCConstants;
use NFC\Headers\NFCInternalConstants;
use NFC\Headers\NFCLogConstants;
use NFC\NFCBaudRates;
use NFC\NFCContext;
use NFC\NFCDevice;
use NFC\NFCDeviceException;
use NFC\NFCDeviceInfo;
use NFC\NFCEventManager;
use NFC\NFCException;
use NFC\NFCModulationTypes;
use NFC\NFCTarget;

class LibNFC implements DriverInterface
{
    protected ?CData $context = null;
    protected ?NFCContext $NFCContext = null;
    protected bool $enableContinuousTouchAdjustment = true;

    protected int $maxFetchDevices = NFCInternalConstants::DEVICE_PORT_LENGTH;
    protected bool $isOpened = false;
    protected int $libNFCLogLevel = NFCLogConstants::NFC_LOG_PRIORITY_NONE;

    protected int $waitPresentationReleaseInterval = 250;

    protected int $pollingContinuations = 0xff;
    protected int $pollingInterval = 2;

    protected NFCBaudRates $baudRates;
    protected NFCModulationTypes $modulationTypes;

    public function open(): self
    {
        // Set libnfc log level;
        putenv("LIBNFC_LOG_LEVEL={$this->libNFCLogLevel}");

        $this->context = $this->NFCContext->getFFI()->new('nfc_context *');

        $this->NFCContext->getFFI()->nfc_init(\FFI::addr($this->context));

        $this->isOpened = true;

        $this->NFCContext->getEventManager()
            ->dispatchEvent(
                NFCEventManager::EVENT_OPEN,
                $this->NFCContext,
            );

        return $this;
    }

    // second
    protected int $continuousTouchAdjustmentExpires = 10;

    public function __construct(NFCContext $context)
    {
        $this->NFCContext = $context;

        $this->baudRates = new NFCBaudRates($this->NFCContext->getFFI());
        $this->modulationTypes = new NFCModulationTypes($this->NFCContext->getFFI());
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

    public function close(): void
    {
        $this->validateContextOpened();

        $this->NFCContext->getFFI()->nfc_exit($this->context);
        $this->NFCContext->getEventManager()
            ->dispatchEvent(
                NFCEventManager::EVENT_CLOSE,
                $this->NFCContext,
            );

        $this->context = null;
        $this->isOpened = false;
    }

    public function getVersion(): string
    {
        $this->validateContextOpened();

        return $this
            ->NFCContext->getFFI()
            ->nfc_version();
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

    public function start(NFCDevice $device = null, NFCModulations $modulations = null): void
    {
        $this->validateContextOpened();

        if ($device === null) {
            $device = $this->getDevices()[0] ?? null;

            if ($device === null) {
                throw new NFCException('NFC Device not found.');
            }
        }

        $this->NFCContext->getEventManager()
            ->dispatchEvent(
                NFCEventManager::EVENT_START,
                $this->NFCContext,
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
                    $this->NFCContext->getEventManager()
                        ->dispatchEvent(
                            NFCEventManager::EVENT_MISSING,
                            $this->NFCContext,
                            $device
                        );
                    continue;
                }

                $target = new NFCTarget(
                    $this->NFCContext,
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

                $this->NFCContext->getEventManager()
                    ->dispatchEvent(
                        NFCEventManager::EVENT_TOUCH,
                        $this->NFCContext,
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

                    $this->NFCContext->getEventManager()
                        ->dispatchEvent(
                            NFCEventManager::EVENT_RELEASE,
                            $this->NFCContext,
                            $target
                        );
                } catch (\Throwable $e) {
                    throw $e;
                }
            } catch (\Throwable $e) {
                $this->NFCContext->getEventManager()
                    ->dispatchEvent(
                        NFCEventManager::EVENT_ERROR,
                        $this->NFCContext,
                        $e
                    );
            }
        }
    }

    public function isPresent(NFCDevice $device, NFCTarget $target): bool
    {
        $isPresent = $this->NFCContext->getFFI()
                ->nfc_initiator_target_is_present(
                    $device->getDeviceContext(),
                    \FFI::addr($target->getNFCTargetContext()->getContext())
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
            ->NFCContext->getFFI()
            ->new('nfc_target');

        $result = $this
            ->NFCContext->getFFI()
            ->nfc_initiator_poll_target(
                $device->getDeviceContext(),
                $modulations->toCDataStructure($this->NFCContext->getFFI()),
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

    /**
     * @return array<NFCDeviceInfo>
     */
    public function getDevices(bool $includeCannotOpenDevices = false): array
    {
        $this->validateContextOpened();

        $connectionTargets = $this
            ->NFCContext->getFFI()
            ->new("nfc_connstring[{$this->maxFetchDevices}]");

        $this
            ->NFCContext->getFFI()
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
                            $nfcDevice = (new NFCDevice($this->NFCContext))
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

    public function getNFCContext(): CData
    {
        return $this->context;
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

    public function setLibNFCLogLevel(int $level): self
    {
        $this->libNFCLogLevel = $level;
        return $this;
    }

    public function getBaudRates(): NFCBaudRates
    {
        return $this->baudRates;
    }

    public function getModulationsTypes(): NFCModulationTypes
    {
        return $this->modulationTypes;
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

    public function getPollingContinuations(): int
    {
        return $this->pollingContinuations;
    }

    public function getPollingInterval(): int
    {
        return $this->pollingInterval;
    }
}
