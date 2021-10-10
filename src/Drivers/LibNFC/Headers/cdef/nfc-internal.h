/*-
 * Free/Libre Near Field Communication (NFC) library
 *
 * Libnfc historical contributors:
 * Copyright (C) 2009      Roel Verdult
 * Copyright (C) 2009-2013 Romuald Conty
 * Copyright (C) 2010-2012 Romain Tartière
 * Copyright (C) 2010-2013 Philippe Teuwen
 * Copyright (C) 2012-2013 Ludovic Rousseau
 * See AUTHORS file for a more comprehensive list of contributors.
 * Additional contributors of this file:
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

/*
 * Buffer management macros.
 *
 * The following macros ease setting-up and using buffers:
 * BUFFER_INIT (data, 5);      // data -> [ xx, xx, xx, xx, xx ]
 * BUFFER_SIZE (data);         // size -> 0
 * BUFFER_APPEND (data, 0x12); // data -> [ 12, xx, xx, xx, xx ]
 * BUFFER_SIZE (data);         // size -> 1
 * uint16_t x = 0x3456;        // We suppose we are little endian
 * BUFFER_APPEND_BYTES (data, x, 2);
 *                             // data -> [ 12, 56, 34, xx, xx ]
 * BUFFER_SIZE (data);         // size -> 3
 * BUFFER_APPEND_LE (data, x, 2, sizeof (x));
 *                             // data -> [ 12, 56, 34, 34, 56 ]
 * BUFFER_SIZE (data);         // size -> 5
 */

typedef enum {
  NOT_INTRUSIVE,
  INTRUSIVE,
  NOT_AVAILABLE,
} scan_type_enum;

struct nfc_user_defined_device {
  char name[DEVICE_NAME_LENGTH];
  nfc_connstring connstring;
  bool optional;
};

/**
 * @struct nfc_context
 * @brief NFC library context
 * Struct which contains internal options, references, pointers, etc. used by library
 */
struct nfc_context {
  bool allow_autoscan;
  bool allow_intrusive_scan;
  uint32_t  log_level;
  struct nfc_user_defined_device user_defined_devices[MAX_USER_DEFINED_DEVICES];
  unsigned int user_defined_device_count;
};

nfc_context *nfc_context_new(void);
void nfc_context_free(nfc_context *context);

/**
 * @struct nfc_device
 * @brief NFC device information
 */
struct nfc_device {
  const nfc_context *context;
  const struct nfc_driver *driver;
  void *driver_data;
  void *chip_data;

  /** Device name string, including device wrapper firmware */
  char    name[DEVICE_NAME_LENGTH];
  /** Device connection string */
  nfc_connstring connstring;
  /** Is the CRC automaticly added, checked and removed from the frames */
  bool    bCrc;
  /** Does the chip handle parity bits, all parities are handled as data */
  bool    bPar;
  /** Should the chip handle frames encapsulation and chaining */
  bool    bEasyFraming;
  /** Should the chip try forever on select? */
  bool    bInfiniteSelect;
  /** Should the chip switch automatically activate ISO14443-4 when
      selecting tags supporting it? */
  bool    bAutoIso14443_4;
  /** Supported modulation encoded in a byte */
  uint8_t  btSupportByte;
  /** Last reported error */
  int     last_error;
};

nfc_device *nfc_device_new(const nfc_context *context, const nfc_connstring connstring);
void        nfc_device_free(nfc_device *dev);

/** void string_as_boolean(const char *s, bool *value); */

/** void iso14443_cascade_uid(const uint8_t abtUID[], const size_t szUID, uint8_t *pbtCascadedUID, size_t *pszCascadedUID); */

/** void prepare_initiator_data(const nfc_modulation nm, uint8_t **ppbtInitiatorData, size_t *pszInitiatorData); */

/** int connstring_decode(const nfc_connstring connstring, const char *driver_name, const char *bus_name, char **pparam1, char **pparam2); */
