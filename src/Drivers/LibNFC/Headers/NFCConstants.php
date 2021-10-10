<?php

declare(strict_types=1);

namespace NFC\Drivers\LibNFC\Headers;

class NFCConstants extends ConstantsEnum
{
    /** @ingroup error
     * @hideinitializer
     * Success (no error)
     */
    public const NFC_SUCCESS = 0;

    /** @ingroup error
     * @hideinitializer
     * Input / output error, device may not be usable anymore without re-open it
     */
    public const NFC_EIO = -1;

    /** @ingroup error
     * @hideinitializer
     * Invalid argument(s)
     */
    public const NFC_EINVARG = -2;

    /** @ingroup error
     * @hideinitializer
     *  Operation not supported by device
     */
    public const NFC_EDEVNOTSUPP = -3;
    /** @ingroup error
     * @hideinitializer
     * No such device
     */
    public const NFC_ENOTSUCHDEV = -4;

    /** @ingroup error
     * @hideinitializer
     * Buffer overflow
     */
    public const NFC_EOVFLOW = -5;

    /** @ingroup error
     * @hideinitializer
     * Operation timed out
     */
    public const NFC_ETIMEOUT = -6;

    /** @ingroup error
     * @hideinitializer
     * Operation aborted (by user)
     */
    public const NFC_EOPABORTED = -7;

    /** @ingroup error
     * @hideinitializer
     * Not (yet) implemented
     */
    public const NFC_ENOTIMP = -8;

    /** @ingroup error
     * @hideinitializer
     * Target released
     */
    public const NFC_ETGRELEASED = -10;

    /** @ingroup error
     * @hideinitializer
     * Error while RF transmission
     */

    public const NFC_ERFTRANS = -20;
    /** @ingroup error
     * @hideinitializer
     * MIFARE Classic: authentication failed
     */
    public const NFC_EMFCAUTHFAIL = -30;

    /** @ingroup error
     * @hideinitializer
     * Software error (allocation, file/pipe creation, etc.)
     */
    public const NFC_ESOFT = -80;

    /** @ingroup error
     * @hideinitializer
     * Device's internal chip error
     */
    public const NFC_ECHIP = -90;
}
