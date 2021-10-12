<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use FFI\CData;
use NFC\Drivers\DriverInterface;
use NFC\NFCContext;
use NFC\NFCDeviceException;
use NFC\NFCException;
use NFC\Util\Util;

/**
 * @see https://github.com/ysomei/test_getnfcid/blob/master/getdeviceid.cpp
 */
class RCS380Command
{
    public const DEFAULT_TIMEOUT = 10;
    public const MAX_RECEIVED_BUFFER_SIZE = 255;

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
    protected NFCModulationTypes $modulationType;
    protected int $type;

    public function __construct(NFCContext $NFCContext, NFCDevice $NFCDevice)
    {
        $this->NFCContext = $NFCContext;
        $this->NFCDevice = $NFCDevice;

        $this->modulationType = new NFCModulationTypes($this->NFCContext->getFFI());

        // FIXME: Replace to changeable
        $this->type = $this->modulationType->FeliCa;
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

    public function setCommandType(): self
    {
        $this->communicate(
            static::toChar(
                [static::SetCommandType, 0x01],
            )
        );

        return $this;
    }

    public function switchRF(): self
    {
        $this->communicate(
            static::toChar(
                [static::SwitchRF, 0x00],
            )
        );

        return $this;
    }

    public function insertRF(): self
    {
        $byteArray = [];
        $protocol = [
            0x02, 0x00, 0x18, 0x01, 0x01, 0x02, 0x01, 0x03,
            0x00, 0x04, 0x00, 0x05, 0x00, 0x06, 0x00, 0x07,
            0x08, 0x08, 0x00, 0x09, 0x00, 0x0a, 0x00, 0x0b,
            0x00, 0x0c, 0x00, 0x0e, 0x04, 0x0f, 0x00, 0x10,
            0x00, 0x11, 0x00, 0x12, 0x00, 0x13, 0x06
        ];

        switch ($this->type) {
            case $this->modulationType->FeliCa:
                $byteArray = [
                    0x00, 0x01, 0x01, 0x0F, 0x01,
                    ...$protocol,
                    0x02, 0x00, 0x18,
                ];
                break;
            default:
                throw new RCS380CommandException('Specify type is not implemented yet [' . $this->type . ']');
        }

        $this->communicate(
            static::toChar($byteArray)
        );

        return $this;
    }

    public function inCommRF(): self
    {
        $byteArray = [];

        switch ($this->type) {
            case $this->modulationType->FeliCa:
                $byteArray = [
                    static::InCommRF, 0x6e, 0x00, 0x06, 0x00, 0xff, 0xff, 0x01, 0x00,
                ];
                break;
            default:
                throw new RCS380CommandException('Specify type is not implemented yet [' . $this->type . ']');
        }

        $this->communicate(
            static::toChar($byteArray)
        );

        return $this;
    }

    public function communicate(string $commandData, int $timeOut = self::DEFAULT_TIMEOUT)
    {
        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Send Packet: " . Util::toHex($commandData));

        // Send
        $this->sendPacket(
            $commandData,
            $timeOut
        );

        // Receive ACK/NCK
        $receivedACK = $this->receivePacket($timeOut);

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Recv ACK/NCK Packet: " . Util::toHex($receivedACK));

        // Receive response
        $receivedResponse = $this->receivePacket($timeOut);

        $this->NFCContext
            ->getNFC()
            ->getLogger()
            ->info("Recv Response Packet: " . Util::toHex($receivedResponse));

        return $receivedResponse;
    }

    public function sendPacket(string $commandData, int $timeOut = self::DEFAULT_TIMEOUT): int
    {
        return $this->sendRawPacket(
            static::encode($commandData),
            $timeOut
        );
    }

    public function sendRawPacket(string $commandData, int $timeOut = self::DEFAULT_TIMEOUT): int
    {
        $length = $this->NFCContext
            ->getFFI()
            ->new('int');

        $command = Util::atoi($commandData);

        $errorCode = $this
            ->NFCContext
            ->getFFI()
            ->libusb_bulk_transfer(
                $this->NFCDevice->getDeviceContext()->getContext(),
                $this->NFCDevice->getPortOut(),
                $command,
                count($commandData),
                \FFI::addr($length),
                $timeOut,
            );

        if ($errorCode < 0) {
            throw new NFCDeviceException(
                "Data send failed [{$errorCode}] on {$this->NFCDevice->getDeviceName()} [{$this->NFCDevice->getConnection()}]"
            );
        }

        return $length->cdata;
    }


    public function receivePacket(int $timeOut = self::DEFAULT_TIMEOUT): string
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
                $this->NFCDevice->getPortIn(),
                $received,
                \FFI::sizeof($received),
                \FFI::addr($length),
                $timeOut,
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
        $packet = '';

        // MAGIC
        $packet .= "\x00\x00\xFF\xFF\xFF";

        // PACKET LENGTH (LOW)
        $packet .= chr($low = strlen($data) & 0xFF);

        // PACKET LENGTH (HIGH)
        $packet .= chr($high = (strlen($data) & 0xFF00) >> 8);

        // CHECKSUM FOR LENGTH
        $packet .= chr(static::calculateChecksum([$low, $high]));

        // PREFIX
        $packet .= "\xD6";

        // DATA
        $packet .= $data;

        // CHECKSUM FOR DATA
        $packet .= chr(static::calculateChecksum(
            array_map(
                'ord',
                str_split($data)
            )
        ));

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
