<?php

declare(strict_types=1);

namespace Tests\Mock;

use NFC\Contexts\FFIContextProxy;
use NFC\Drivers\LibNFC\Headers\NFCInternalConstants;
use NFC\Drivers\LibNFC\Headers\NFCTypesConstants;

class MockedFFIContextProxy extends FFIContextProxy
{
    public function __call($name, $arguments)
    {
        if ($name === 'nfc_list_devices') {
            $arguments[1][0] = static::makeString(
                'dummy-1',
                NFCTypesConstants::NFC_BUFSIZE_CONNSTRING
            );

            $arguments[1][1] = static::makeString(
                'dummy-2',
                NFCTypesConstants::NFC_BUFSIZE_CONNSTRING
            );

            return 1;
        }

        if ($name === 'nfc_initiator_target_is_present') {
            return 0;
        }

        if ($name === 'nfc_open') {
            return $this->ffi->new('nfc_device *');
        }

        if ($name === 'nfc_close') {
            return;
        }

        if ($name === 'nfc_device_get_name') {
            return "dummy-device";
        }

        if ($name === 'nfc_initiator_init') {
            return 0;
        }

        if ($name === 'nfc_initiator_poll_target') {
            return 1;
        }

        if ($name === 'nfc_device_get_last_error') {
            return 0;
        }

        return parent::__call($name, $arguments);
    }

    protected static function makeString(string $from, int $size)
    {
        $from = $from . "\0";
        $string = \FFI::new('char[' . $size . ']');
        for ($i = 0; $i < strlen($from); $i++) {
            $string[$i]->cdata = $from[$i];
        }

        return $string;
    }
}
