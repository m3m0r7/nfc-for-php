<?php

declare(strict_types=1);

namespace NFC\Util;

use FFI\CData;
use NFC\NFCInterface;

class Util
{
    public static function itoa(CData $UIntArray)
    {
        $size = \FFI::sizeof($UIntArray);

        $string = [];
        for ($i = 0; $i < $size; $i++) {
            $string[$i] = chr($UIntArray[$i]);
        }

        return rtrim(implode($string));
    }
}