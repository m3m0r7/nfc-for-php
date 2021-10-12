<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380;

use NFC\Drivers\DriverInterface;

class RCS380CommandList
{
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
}