<?php

declare(strict_types=1);

namespace NFC\Drivers\RCS380\Headers;

use NFC\ConstantsEnum;

class LibUSBConstants extends ConstantsEnum
{
    public const LIBUSB_ISO_USAGE_TYPE_MASK = 0x30;
    public const LIBUSB_ISO_SYNC_TYPE_MASK = 0x0c;
    public const LIBUSB_TRANSFER_TYPE_MASK =  0x03; /* in bmAttributes */

    public const LIBUSB_ENDPOINT_ADDRESS_MASK = 0x0f; /* in bEndpointAddress */
    public const LIBUSB_ENDPOINT_DIR_MASK = 0x80;

    public const LIBUSB_DT_DEVICE_SIZE = 18;
    public const LIBUSB_DT_CONFIG_SIZE = 9;
    public const LIBUSB_DT_INTERFACE_SIZE = 9;
    public const LIBUSB_DT_ENDPOINT_SIZE = 7;
    public const LIBUSB_DT_ENDPOINT_AUDIO_SIZE = 9; /* Audio extension */
    public const LIBUSB_DT_HUB_NONVAR_SIZE = 7;
    public const LIBUSB_DT_SS_ENDPOINT_COMPANION_SIZE = 6;
    public const LIBUSB_DT_BOS_SIZE = 5;
    public const LIBUSB_DT_DEVICE_CAPABILITY_SIZE = 3;

    /* BOS descriptor sizes */
    public const LIBUSB_BT_USB_2_0_EXTENSION_SIZE = 7;
    public const LIBUSB_BT_SS_USB_DEVICE_CAPABILITY_SIZE = 10;
    public const LIBUSB_BT_CONTAINER_ID_SIZE = 20;

    public const LIBUSB_API_VERSION = 0x01000108;

    /* The following is kept for compatibility, but will be deprecated in the future */
    public const LIBUSBX_API_VERSION = self::LIBUSB_API_VERSION;

    public const ZERO_SIZED_ARRAY = 0; /* [0] - non-standard, but usually working code */
}
