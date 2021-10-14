<?php

declare(strict_types=1);

namespace NFC\Util;

use FFI\CData;
use NFC\NFCInterface;

class Util
{
    public static function itoa(CData $UIntArray): string
    {
        $size = \FFI::sizeof($UIntArray);

        $string = [];
        for ($i = 0; $i < $size; $i++) {
            $string[$i] = chr($UIntArray[$i]);
        }

        return rtrim(implode($string));
    }

    public static function atoi(string $string, int $size = null): CData
    {
        $size = ($size === null ? strlen($string) : $size) + 1;
        $uInt8Array = \FFI::new("uint8_t[{$size}]");
        for ($i = 0; $i < $size; $i++) {
            $uInt8Array[$i] = !isset($string[$i])
                ? 0
                : ord($string[$i]);
        }

        return $uInt8Array;
    }

    public static function toHex(string $string): string
    {
        return implode(
            " ",
            array_map(
                static fn ($chunkedArray) => implode(
                    ' ',
                    $chunkedArray
                ),
                array_chunk(
                    array_map(
                        static fn ($char) => sprintf('%02X', ord($char)),
                        str_split($string)
                    ),
                    8
                )
            )
        );
    }
}
