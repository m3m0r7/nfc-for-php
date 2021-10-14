<?php

declare(strict_types=1);

namespace Tests\Mock;

use NFC\Contexts\FFIContextProxy;
use NFC\Drivers\LibNFC\Headers\NFCInternalConstants;
use NFC\Drivers\LibNFC\Headers\NFCTypesConstants;
use NFC\Drivers\RCS380\RCS380Driver;

class MockedRCS380FFIContextProxy extends FFIContextProxy
{
    public function __call($name, $arguments)
    {
        if ($name === 'libusb_get_device_list') {
            return 1;
        }
        if ($name === 'libusb_get_device_descriptor') {
            $arguments[1]->idVendor = RCS380Driver::VENDOR_ID;
            $arguments[1]->idProduct = RCS380Driver::PRODUCT_ID;
            return 0;
        }
        if ($name === 'libusb_get_bus_number') {
            return 1;
        }
        if ($name === 'libusb_get_device_address') {
            return 2;
        }
        if ($name === 'libusb_free_device_list') {
            return null;
        }
        if ($name === 'libusb_open') {
            return 0;
        }
        if ($name === 'libusb_set_auto_detach_kernel_driver') {
            return 0;
        }
        if ($name === 'libusb_set_configuration') {
            return 0;
        }
        if ($name === 'libusb_claim_interface') {
            return 0;
        }
        if ($name === 'libusb_set_interface_alt_setting') {
            return 0;
        }
        if ($name === 'libusb_get_config_descriptor') {
            return 0;
        }
        return parent::__call($name, $arguments);
    }
}