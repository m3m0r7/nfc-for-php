<?php
/**
 * You don't include this file.
 */

namespace NFC\Contexts
{
    /**
     * @method void nfc_context_free(\FFI\CData $context)
     * @method void nfc_device_free(\FFI\CData $dev)
     * @method void string_as_boolean(\FFI\CData $s, \FFI\CData $value)
     * @method void iso14443_cascade_uid(\FFI\CData $abtUID, int $szUID, \FFI\CData $pbtCascadedUID, \FFI\CData $pszCascadedUID)
     * @method void prepare_initiator_data(\FFI\CData $nm, \FFI\CData $ppbtInitiatorData, \FFI\CData $pszInitiatorData)
     * @method int connstring_decode(\FFI\CData $connstring, \FFI\CData $driver_name, \FFI\CData $bus_name, \FFI\CData $pparam1, \FFI\CData $pparam2)
     * @method void nfc_init(\FFI\CData $context)
     * @method void nfc_exit(\FFI\CData $context)
     * @method int nfc_register_driver(\FFI\CData $driver)
     * @method void nfc_close(\FFI\CData $pnd)
     * @method int nfc_abort_command(\FFI\CData $pnd)
     * @method int nfc_list_devices(\FFI\CData $context, \FFI\CData $connstrings, int $connstrings_len)
     * @method int nfc_idle(\FFI\CData $pnd)
     * @method int nfc_initiator_init(\FFI\CData $pnd)
     * @method int nfc_initiator_init_secure_element(\FFI\CData $pnd)
     * @method int nfc_initiator_select_passive_target(\FFI\CData $pnd, \FFI\CData $nm, \FFI\CData $pbtInitData, int $szInitData, \FFI\CData $pnt)
     * @method int nfc_initiator_list_passive_targets(\FFI\CData $pnd, \FFI\CData $nm, \FFI\CData $ant, int $szTargets)
     * @method int nfc_initiator_poll_target(\FFI\CData $pnd, \FFI\CData $pnmTargetTypes, int $szTargetTypes, \FFI\CData $uiPollNr, \FFI\CData $uiPeriod, \FFI\CData $pnt)
     * @method int nfc_initiator_select_dep_target(\FFI\CData $pnd, \FFI\CData $ndm, \FFI\CData $nbr, \FFI\CData $pndiInitiator, \FFI\CData $pnt, int $timeout)
     * @method int nfc_initiator_poll_dep_target(\FFI\CData $pnd, \FFI\CData $ndm, \FFI\CData $nbr, \FFI\CData $pndiInitiator, \FFI\CData $pnt, int $timeout)
     * @method int nfc_initiator_deselect_target(\FFI\CData $pnd)
     * @method int nfc_initiator_transceive_bytes(\FFI\CData $pnd, \FFI\CData $pbtTx, int $szTx, \FFI\CData $pbtRx, int $szRx, int $timeout)
     * @method int nfc_initiator_transceive_bits(\FFI\CData $pnd, \FFI\CData $pbtTx, int $szTxBits, \FFI\CData $pbtTxPar, \FFI\CData $pbtRx, int $szRx, \FFI\CData $pbtRxPar)
     * @method int nfc_initiator_transceive_bytes_timed(\FFI\CData $pnd, \FFI\CData $pbtTx, int $szTx, \FFI\CData $pbtRx, int $szRx, \FFI\CData $cycles)
     * @method int nfc_initiator_transceive_bits_timed(\FFI\CData $pnd, \FFI\CData $pbtTx, int $szTxBits, \FFI\CData $pbtTxPar, \FFI\CData $pbtRx, int $szRx, \FFI\CData $pbtRxPar, \FFI\CData $cycles)
     * @method int nfc_initiator_target_is_present(\FFI\CData $pnd, \FFI\CData $pnt)
     * @method int nfc_target_init(\FFI\CData $pnd, \FFI\CData $pnt, \FFI\CData $pbtRx, int $szRx, int $timeout)
     * @method int nfc_target_send_bytes(\FFI\CData $pnd, \FFI\CData $pbtTx, int $szTx, int $timeout)
     * @method int nfc_target_receive_bytes(\FFI\CData $pnd, \FFI\CData $pbtRx, int $szRx, int $timeout)
     * @method int nfc_target_send_bits(\FFI\CData $pnd, \FFI\CData $pbtTx, int $szTxBits, \FFI\CData $pbtTxPar)
     * @method int nfc_target_receive_bits(\FFI\CData $pnd, \FFI\CData $pbtRx, int $szRx, \FFI\CData $pbtRxPar)
     * @method int nfc_strerror_r(\FFI\CData $pnd, \FFI\CData $buf, int $buflen)
     * @method void nfc_perror(\FFI\CData $pnd, \FFI\CData $s)
     * @method int nfc_device_get_last_error(\FFI\CData $pnd)
     * @method int nfc_device_get_supported_modulation(\FFI\CData $pnd, \FFI\CData $mode, \FFI\CData $supported_mt)
     * @method int nfc_device_get_supported_baud_rate(\FFI\CData $pnd, \FFI\CData $nmt, \FFI\CData $supported_br)
     * @method int nfc_device_get_supported_baud_rate_target_mode(\FFI\CData $pnd, \FFI\CData $nmt, \FFI\CData $supported_br)
     * @method int nfc_device_set_property_int(\FFI\CData $pnd, \FFI\CData $property, int $value)
     * @method int nfc_device_set_property_bool(\FFI\CData $pnd, \FFI\CData $property, bool $bEnable)
     * @method void iso14443a_crc(\FFI\CData $pbtData, int $szLen, \FFI\CData $pbtCrc)
     * @method void iso14443a_crc_append(\FFI\CData $pbtData, int $szLen)
     * @method void iso14443b_crc(\FFI\CData $pbtData, int $szLen, \FFI\CData $pbtCrc)
     * @method void iso14443b_crc_append(\FFI\CData $pbtData, int $szLen)
     * @method void nfc_free(\FFI\CData $p)
     * @method int nfc_device_get_information_about(\FFI\CData $pnd, \FFI\CData $buf)
     * @method int str_nfc_target(\FFI\CData $buf, \FFI\CData $pnt, bool $verbose)
     */
    class FFIContextProxy implements ContextProxyInterface
    {
        public function __get($name)
        {
        }
    }
}
