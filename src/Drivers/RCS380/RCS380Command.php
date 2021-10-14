<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use FFI\CData;
use NFC\Collections\NFCModulationsInterface;
use NFC\Drivers\DriverInterface;
use NFC\NFCContext;
use NFC\NFCDeviceException;
use NFC\NFCException;
use NFC\NFCModulation;
use NFC\Util\Util;

/**
 * @see https://github.com/nfcpy/nfcpy/blob/master/src/nfc/clf/rcs380.py
 * @see https://github.com/ysomei/test_getnfcid/blob/master/getdeviceid.cpp
 * @see https://qiita.com/saturday06/items/333fcdf5b3b8030c9b05
 */
class RCS380Command
{
    public const MAX_RECEIVED_BUFFER_SIZE = 255;
    public const TRY_MIXED_POLL_COUNT = 5;
    public const WAIT_RETRY_TIME = 2;

    public const MAGIC = "\x00\x00\xFF\xFF\xFF";
    protected const NOP = "\x00\x00\xFF\x00\xFF";

    // The Commands List
    public const InSetRF = 0x00;
    public const InSetProtocol = 0x02;
    public const InCommRF = 0x04;
    public const SwitchRF = 0x06;
    public const MaintainFlash = 0x10;
    public const ResetDevice = 0x12;
    public const GetFirmwareVersion = 0x20;
    public const GetPDDataVersion = 0x22;
    public const GetProperty = 0x24;
    public const InGetProtocol = 0x26;
    public const GetCommandType = 0x28;
    public const SetCommandType = 0x2A;
    public const InSetRCT = 0x30;
    public const InGetRCT = 0x32;
    public const GetPDData = 0x34;
    public const ReadRegister = 0x36;
    public const TgSetRF = 0x40;
    public const TgSetProtocol = 0x42;
    public const TgSetAuto = 0x44;
    public const TgSetRFOff = 0x46;
    public const TgCommRF = 0x48;
    public const TgGetProtocol = 0x50;
    public const TgSetRCT = 0x60;
    public const TgGetRCT = 0x62;
    public const Diagnose = 0xF0;

    protected NFCContext $NFCContext;
    protected NFCDevice $NFCDevice;
    protected NFCModulationTypes $modulationTypes;
    protected NFCModulationsInterface $modulations;
    protected RCS380Driver $driver;

    protected int $timeout = 10;

    public function __construct(RCS380Driver $driver, NFCModulationsInterface $modulations, NFCContext $NFCContext, NFCDevice $NFCDevice)
    {
        $this->driver = $driver;
        $this->NFCContext = $NFCContext;
        $this->NFCDevice = $NFCDevice;

        $this->modulationTypes = new NFCModulationTypes($NFCContext->getFFI());
        $this->modulations = $modulations;
    }

    public function init(): self
    {
        $this->sendRawPacket(
            static::toChar(
                [0x00, 0x00, 0xFF, 0x00, 0xFF, 0x00],
            )
        );

        return $this;
    }

    public function setCommandType()
    {
        $received = $this->communicate(
            static::toChar(
                [static::SetCommandType, 0x01],
            )
        );

        $expectedReceivedPacket = static::toChar([
            0x00, 0x00, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFD,
            0xD7, 0x2B, 0x00, 0xFE,
        ]);
        if ($received !== $expectedReceivedPacket) {
            throw new ReceivePacketException("Response packet is invalid [" . Util::toHex($received, false) . "/" . Util::toHex($expectedReceivedPacket, false) . "]");
        }

        return $received;
    }

    public function switchRF()
    {
        $received = $this->communicate(
            static::toChar(
                [static::SwitchRF, 0x00],
            )
        );

        $expectedReceivedPacket = static::toChar([
            0x00, 0x00, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFD,
            0xD7, 0x07, 0x00, 0x22
        ]);
        if ($received !== static::NOP && $received !== $expectedReceivedPacket) {
            throw new ReceivePacketException("Response packet is invalid [" . Util::toHex($received, false) . "/" . Util::toHex($expectedReceivedPacket, false) . "]");
        }

        return $received;
    }

    public function inSetRF(int $type)
    {
        $byteArray = [];

        switch ($type) {
            case $this->modulationTypes->NMT_FELICA:
                $byteArray = [
                    0x00, 0x01, 0x01, 0x0F, 0x01,
                ];
                break;
            default:
                throw new RCS380CommandException('Specify type is not implemented yet [' . $this->type . ']');
        }

        $received = $this->communicate(
            static::toChar($byteArray)
        );

        $expectedReceivedPacket = static::toChar([
            0x00, 0x00, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFD, 0xD7, 0x01, 0x00, 0x28
        ]);
        if ($received !== static::NOP && $received !== $expectedReceivedPacket) {
            throw new ReceivePacketException("Response packet is invalid [" . Util::toHex($received, false) . "/" . Util::toHex($expectedReceivedPacket, false) . "]");
        }

        return $received;
    }

    public function inSetProtocol1()
    {
        $received = $this->communicate(
            static::toChar(
                [
                static::InSetProtocol, 0x00, 0x18, 0x01, 0x01, 0x02, 0x01, 0x03,
                0x00, 0x04, 0x00, 0x05, 0x00, 0x06, 0x00, 0x07,
                0x08, 0x08, 0x00, 0x09, 0x00, 0x0a, 0x00, 0x0b,
                0x00, 0x0c, 0x00, 0x0e, 0x04, 0x0f, 0x00, 0x10,
                0x00, 0x11, 0x00, 0x12, 0x00, 0x13, 0x06
                ]
            )
        );

        $expectedReceivedPacket1 = static::toChar([
            0x00, 0x00, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFD, 0xD7
        ]);
        $expectedReceivedPacket2 = static::toChar([
            0x00, 0x26
        ]);
        if ($received !== static::NOP && substr($received, 0, strlen($expectedReceivedPacket1)) !== $expectedReceivedPacket1 || substr($received, -1 * strlen($expectedReceivedPacket2)) !== $expectedReceivedPacket2) {
            throw new ReceivePacketException("Response packet is invalid [" . Util::toHex($received, false) . "]");
        }

        return $received;
    }

    public function inSetProtocol2(int $type)
    {
        $byteArray = [];
        switch ($type) {
            case $this->modulationTypes->NMT_FELICA:
                $byteArray = [
                    0x00, 0x01, 0x01, 0x0F, 0x01, 0x02, 0x00, 0x18,
                ];
                break;
            default:
                throw new RCS380CommandException('Specify type is not implemented yet [' . $type . ']');
        }

        $received = $this->communicate(
            static::toChar($byteArray),
            false
        );

        $expectedReceivedPacket = static::toChar([
            0x00, 0x00, 0xFF, 0x01, 0xFF, 0x7F, 0x81
        ]);
        if ($received !== static::NOP && $received !== $expectedReceivedPacket) {
            throw new ReceivePacketException("Response packet is invalid [" . Util::toHex($received, false) . "/" . Util::toHex($expectedReceivedPacket, false) . "]");
        }

        return $received;
    }

    public function sensfReq(?NFCModulation $default = null): array
    {
        /**
         * @var NFCModulation $modulation
         */
        while (true) {
            foreach ($this->modulations as $modulation) {

                if ($default !== null) {
                    if ($default->getModulationType() !== $modulation->getModulationType() || $default->getBaudRate() !== $modulation->getBaudRate()) {
                        continue;
                    }
                }

                $this->switchRF();
                $this->inSetRF($modulation->getModulationType());
                $this->inSetProtocol1();
                $this->inSetProtocol2($modulation->getModulationType());

                $remainingTries = static::TRY_MIXED_POLL_COUNT;
                do {
                    $response = $this->inCommRF($modulation->getModulationType());
                    if ($response !== null) {
                        return [
                            $modulation,
                            $response,
                        ];
                    }
                } while ($response === null && $remainingTries-- >= 0);
                sleep(static::WAIT_RETRY_TIME);
            }
        }
    }

    public function inCommRF(int $type): ?string
    {
        $byteArray = [];

        switch ($type) {
        case $this->modulationTypes->NMT_FELICA:
            $byteArray = [
                static::InCommRF, 0x6e, 0x00, 0x06, 0x00, 0xff, 0xff, 0x01, 0x00,
            ];
            break;
        default:
            throw new RCS380CommandException('Specify type is not implemented yet [' . $type . ']');
        }

        try {
            return $this->communicate(
                static::toChar($byteArray),
                false
            );
        } catch (NFCDeviceException $e) {
            return null;
        }
    }

    public function communicate(string $commandData, bool $validateMagic = true): string
    {
        $commandType = 'Unknown';
        if (isset($commandData[0])) {
            $commandType = array_search(
                ord($commandData[0]),
                (new \ReflectionClass($this))
                ->getConstants()
            ) ?? 'Unknown';
        }

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Send Packet: {$commandType} " . Util::toHex($commandData));

        // Send
        $this->sendPacket(
            $commandData,
            $this->timeout
        );

        // Receive ACK/NCK
        $receivedACK = $this->receivePacket($this->timeout);

        // Packet validation
//        if ($receivedACK !== static::NOP) {
//            throw new ReceivePacketException("ACK/NCK packet is invalid [" . Util::toHex($receivedACK, false) . "]");
//        }

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Recv ACK/NCK Packet: " . Util::toHex($receivedACK));

        // Receive response
        $receivedResponse = $this->receivePacket($this->timeout);

//        if ($validateMagic) {
//            if ($receivedResponse !== static::NOP && substr($receivedResponse, 0, strlen(static::MAGIC)) !== static::MAGIC) {
//                throw new ReceivePacketException("Response packet is invalid [" . Util::toHex($receivedResponse, false) . "]");
//            }
//        }

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Recv Response Packet: " . Util::toHex($receivedResponse));

        return $receivedResponse;
    }

    public function sendPacket(string $commandData): int
    {
        return $this->sendRawPacket(
            static::encode($commandData)
        );
    }

    public function sendRawPacket(string $commandData): int
    {
        $length = $this->NFCContext
            ->getFFI()
            ->new('int');

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->debug("Send raw packet: " . Util::toHex($commandData));

        $command = Util::atoi($commandData);

        $errorCode = $this
            ->NFCContext
            ->getFFI()
            ->libusb_bulk_transfer(
                $this->NFCDevice->getDeviceContext()->getContext(),
                $this->NFCDevice->getTransportOut(),
                $command,
                strlen($commandData),
                \FFI::addr($length),
                $this->timeout,
            );

        if ($errorCode < 0) {
            throw new NFCDeviceException(
                "Data send failed [{$errorCode}] on {$this->NFCDevice->getDeviceName()} [{$this->NFCDevice->getConnection()}]"
            );
        }

        return $length->cdata;
    }


    public function receivePacket(): string
    {
        $received = $this->NFCContext
            ->getFFI()
            ->new('uint8_t[' . static::MAX_RECEIVED_BUFFER_SIZE . ']');

        $length = $this->NFCContext
            ->getFFI()
            ->new('int');

        $errorCode = $this
            ->NFCContext
            ->getFFI()
            ->libusb_bulk_transfer(
                $this->NFCDevice->getDeviceContext()->getContext(),
                $this->NFCDevice->getTransportIn(),
                $received,
                \FFI::sizeof($received),
                \FFI::addr($length),
                $this->timeout,
            );

        if ($errorCode < 0) {
            throw new NFCDeviceException(
                "Data recv failed [{$errorCode}] on {$this->NFCDevice->getDeviceName()} [{$this->NFCDevice->getConnection()}]"
            );
        }

        return Util::itoa($received);
    }

    protected static function encode(string $data): string
    {
        $data = "\xD6" . $data;

        $packet = '';

        // MAGIC
        $packet .= static::MAGIC;

        // PACKET LENGTH (LOW)
        $packet .= chr($low = strlen($data) & 0xFF);

        // PACKET LENGTH (HIGH)
        $packet .= chr($high = (strlen($data) & 0xFF00) >> 8);

        // CHECKSUM FOR LENGTH
        $packet .= chr(static::calculateChecksum([$low, $high]));

        // DATA
        $packet .= $data;

        // CHECKSUM FOR DATA
        $packet .= chr(
            static::calculateChecksum(
                array_map(
                    'ord',
                    str_split($data)
                )
            )
        );

        // FOOTER
        $packet .= "\x00";

        return $packet;
    }

    protected static function calculateChecksum(array $data): int
    {
        $sum = 0;
        for ($i = 0; $i < count($data); $i++) {
            $sum += $data[$i];
        }

        return (0x100 - $sum) % 0x100;
    }

    protected static function toChar(array $hexArray): string
    {
        return pack(
            "C*",
            ...$hexArray
        );
    }
}
