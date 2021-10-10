<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC;

use FFI\CData;
use NFC\Collections\NFCModulations;
use NFC\Contexts\ContextProxyInterface;
use NFC\Contexts\NFCContextContextProxy;
use NFC\Contexts\NFCTargetContextProxy;
use NFC\Drivers\DriverInterface;
use NFC\Drivers\LibNFC\NFCDevice;
use NFC\Drivers\LibNFC\NFCTarget;
use NFC\Drivers\LibNFC\Headers\NFCConstants;
use NFC\Drivers\LibNFC\Headers\NFCInternalConstants;
use NFC\Drivers\LibNFC\Headers\NFCLogConstants;
use NFC\NFCBaudRates;
use NFC\NFCContext;
use NFC\NFCDeviceException;
use NFC\NFCDeviceInfo;
use NFC\NFCDeviceInterface;
use NFC\NFCEventManager;
use NFC\NFCException;
use NFC\NFCModulationTypes;
use NFC\NFCTargetInterface;

class LibNFC implements DriverInterface
{
    protected ?ContextProxyInterface $context = null;
    protected ?NFCContext $NFCContext = null;
    protected bool $enableContinuousTouchAdjustment = true;

    protected int $maxFetchDevices = NFCInternalConstants::DEVICE_PORT_LENGTH;
    protected bool $isOpened = false;
    protected int $libNFCLogLevel = NFCLogConstants::NFC_LOG_PRIORITY_NONE;

    protected int $waitPresentationReleaseInterval = 250;

    protected int $pollingContinuations = 0xff;
    protected int $pollingInterval = 2;

    // second
    protected int $continuousTouchAdjustmentExpires = 10;

    protected NFCBaudRates $baudRates;
    protected NFCModulationTypes $modulationTypes;

    public function open(): self
    {
        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Open the libnfc: Log Level [$this->libNFCLogLevel]");

        // Set libnfc log level;
        putenv("LIBNFC_LOG_LEVEL={$this->libNFCLogLevel}");

        $this->context = new NFCContextContextProxy($this->NFCContext->getFFI()->new('nfc_context *'));
        $this->NFCContext->getFFI()->nfc_init(\FFI::addr($this->context->getContext()));

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Initialized NFC");

        $this->isOpened = true;

        return $this;
    }

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

        $this->NFCContext->getFFI()->nfc_exit($this->context->getContext());
        $this->context = null;
        $this->isOpened = false;

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("NFC Exit");
    }

    public function getVersion(): string
    {
        $this->validateContextOpened();

        return $this
            ->NFCContext->getFFI()
            ->nfc_version();
    }

    public function findDeviceNameContain(string $deviceName): NFCDeviceInterface
    {
        $this->validateContextOpened();

        $exceptions = [];
        try {
            /**
             * @var NFCDeviceInfo $NFCDevice
             */
            foreach ($this->getDevices() as $NFCDevice) {
                if (strpos($NFCDevice->getDeviceName(), $deviceName) !== false) {
                    return $NFCDevice->getDevice();
                }
            }
        } catch (NFCDeviceException $e) {
            $exceptions[] = $e;
        }

        if (count($exceptions) === 0) {
            throw new NFCException('Unable to find available device');
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

    public function start(NFCDeviceInterface $device = null, NFCModulations $modulations = null): void
    {
        $this->validateContextOpened();

        if ($device === null) {
            $device = $this->getDevices()[0] ?? null;

            if ($device === null) {
                throw new NFCException('Available NFC device not found');
            }
        }

        $this->NFCContext->getEventManager()
            ->dispatchEvent(
                NFCEventManager::EVENT_START,
                $this->NFCContext,
                $device
            );

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Start to listen");

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
                if (($NFCTargetContext = $this->poll($device, $modulations)) === null) {
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
                    $NFCTargetContext
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

                $this->NFCContext
                    ->getEventManager()
                    ->dispatchEvent(
                        NFCEventManager::EVENT_TOUCH,
                        $this->NFCContext,
                        $target
                    );

                $this->NFCContext
                    ->getNFC()
                    ->getLogger()
                    ->info("Touched target: {$target->getAttributeAccessor()->getID()}");

                if ($this->enableContinuousTouchAdjustment) {
                    $touched = $info + [
                        'expires' => time() + $this->continuousTouchAdjustmentExpires,
                    ];
                }

                try {
                    while ($this->isPresent($device, $target)) {
                        usleep($this->waitPresentationReleaseInterval);
                    }

                    $this->NFCContext
                        ->getEventManager()
                        ->dispatchEvent(
                            NFCEventManager::EVENT_RELEASE,
                            $this->NFCContext,
                            $target
                        );

                    $this->NFCContext
                        ->getNFC()
                        ->getLogger()
                        ->info("Released target: {$target->getAttributeAccessor()->getID()}");
                } catch (\Throwable $e) {
                    throw $e;
                }
            } catch (\Throwable $e) {
                $this->NFCContext
                    ->getEventManager()
                    ->dispatchEvent(
                        NFCEventManager::EVENT_ERROR,
                        $this->NFCContext,
                        $e
                    );
            }
        }
    }

    public function isPresent(NFCDeviceInterface $device, NFCTargetInterface $target): bool
    {
        $isPresent = $this->NFCContext
                ->getFFI()
                ->nfc_initiator_target_is_present(
                    $device->getDeviceContext()->getContext(),
                    \FFI::addr($target->getNFCTargetContext()->getContext())
                ) === 0;

        if ($device->getLastErrorCode() !== NFCConstants::NFC_ETGRELEASED) {
            throw new NFCException(
                "An error occurred: {$device->getLastErrorName()}({$device->getLastErrorCode()})"
            );
        }

        return $isPresent;
    }

    public function poll(NFCDeviceInterface $device, NFCModulations $modulations): ?ContextProxyInterface
    {
        $NFCTargetContext = $this
            ->NFCContext->getFFI()
            ->new('nfc_target');

        $result = $this
            ->NFCContext->getFFI()
            ->nfc_initiator_poll_target(
                $device->getDeviceContext()->getContext(),
                $modulations->toCDataStructure($this->NFCContext->getFFI()),
                count($modulations),
                $this->pollingContinuations,
                $this->pollingInterval,
                \FFI::addr($NFCTargetContext),
            );

        if ($result <= 0) {
            return null;
        }

        return new NFCTargetContextProxy($NFCTargetContext);
    }

    /**
     * @return array<NFCDeviceInfo>
     */
    public function getDevices(bool $includeCannotOpenDevices = false): array
    {
        $this->validateContextOpened();

        $connectionTargets = $this
            ->NFCContext
            ->getFFI()
            ->new("nfc_connstring[{$this->maxFetchDevices}]");

        $this
            ->NFCContext
            ->getFFI()
            ->nfc_list_devices(
                $this->context->getContext(),
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

        $devices = array_values(
            array_filter(
                array_map(
                    function (string $connectionTarget) use ($includeCannotOpenDevices) {
                        try {
                            $NFCDevice = (new NFCDevice($this->NFCContext))
                                ->open($connectionTarget, $includeCannotOpenDevices);
                        } catch (NFCDeviceException $e) {
                            return null;
                        }

                        return new NFCDeviceInfo(
                            $connectionTarget,
                            $NFCDevice
                        );
                    },
                    $data,
                )
            )
        );

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Available devices found: " . count($devices));

        return $devices;
    }

    public function getNFCContext(): ContextProxyInterface
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
            $this->NFCContext->open();
        }

        if ($this->context === null) {
            throw new NFCException('Closed the context');
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
