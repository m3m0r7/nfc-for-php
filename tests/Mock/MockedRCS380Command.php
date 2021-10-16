<?php

declare(strict_types=1);

namespace Tests\Mock;

use NFC\Drivers\RCS380\RCS380Command;

class MockedRCS380Command extends RCS380Command
{
    protected bool $enableValidatingPacket = false;

    public function communicate(string $commandData, bool $validateMagic = true): string
    {
        // SetCommandType
        if ($commandData === static::toChar([static::SetCommandType, 0x01])) {
            return static::toChar(
                [
                0x00, 0x00, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFD,
                0xD7, 0x2B, 0x00, 0xFE,
                ]
            );
        }

        // SwitchRF
        if ($commandData === static::toChar([static::SwitchRF, 0x00])) {
            return static::toChar(
                [
                0x00, 0x00, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFD,
                0xD7, 0x07, 0x00, 0x22
                ]
            );
        }

        // InSetRF - 1
        if ($commandData === static::toChar([0x00, 0x01, 0x01, 0x0F, 0x01])) {
            return static::toChar(
                [
                0x00, 0x00, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFD, 0xD7, 0x01, 0x00, 0x28
                ]
            );
        }

        // InSetRF - 2
        if ($commandData === static::toChar(
            [
            static::InSetProtocol, 0x00, 0x18, 0x01, 0x01, 0x02, 0x01, 0x03,
            0x00, 0x04, 0x00, 0x05, 0x00, 0x06, 0x00, 0x07,
            0x08, 0x08, 0x00, 0x09, 0x00, 0x0a, 0x00, 0x0b,
            0x00, 0x0c, 0x00, 0x0e, 0x04, 0x0f, 0x00, 0x10,
            0x00, 0x11, 0x00, 0x12, 0x00, 0x13, 0x06
            ]
        )
        ) {
            return static::toChar(
                [
                0x00, 0x00, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFD, 0xD7,
                0x00, 0x26,
                ]
            );
        }

        // InSetRF - 3
        if ($commandData === static::toChar([0x00, 0x01, 0x01, 0x0F, 0x01, 0x02, 0x00, 0x18])) {
            return static::toChar(
                [
                0x00, 0x00, 0xFF, 0x01, 0xFF, 0x7F, 0x81
                ]
            );
        }

        // InCommRF
        if ($commandData === static::toChar([static::InCommRF, 0x6e, 0x00, 0x06, 0x00, 0xff, 0xff, 0x01, 0x00])) {
            return static::toChar(
                [
                0x00, 0x00, 0xFF, 0xFF, 0xFF,

                // Null filled dummy packet
                0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
                0x00, 0x00, 0x00, 0x00,

                // IDm
                0x12, 0x34, 0x56, 0x78, 0x91, 0x01, 0x11, 0x21,

                // Pad
                0x12, 0x34, 0x56, 0x78, 0x91, 0x01, 0x11, 0x21,

                // SC
                0x12, 0x34,
                ]
            );
        }

        return static::toChar([0x00]);
    }

    public function sendRawPacket(string $commandData): int
    {
        return 0;
    }
}
