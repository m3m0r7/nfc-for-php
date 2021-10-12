/*
 * Public libusb header file
 * Copyright © 2001 Johannes Erdfelt <johannes@erdfelt.com>
 * Copyright © 2007-2008 Daniel Drake <dsd@gentoo.org>
 * Copyright © 2012 Pete Batard <pete@akeo.ie>
 * Copyright © 2012-2018 Nathan Hjelm <hjelmn@cs.unm.edu>
 * Copyright © 2014-2020 Chris Dickens <christopher.a.dickens@gmail.com>
 * For more information, please visit: http://libusb.info
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

/** \def LIBUSB_CALL
 * \ingroup libusb_misc
 * libusb's Windows calling convention.
 *
 * Under Windows, the selection of available compilers and configurations
 * means that, unlike other platforms, there is not <em>one true calling
 * convention</em> (calling convention: the manner in which parameters are
 * passed to functions in the generated assembly code).
 *
 * Matching the Windows API itself, libusb uses the WINAPI convention (which
 * translates to the <tt>stdcall</tt> convention) and guarantees that the
 * library is compiled in this way. The public header file also includes
 * appropriate annotations so that your own software will use the right
 * convention, even if another convention is being used by default within
 * your codebase.
 *
 * The one consideration that you must apply in your software is to mark
 * all functions which you use as libusb callbacks with this LIBUSB_CALL
 * annotation, so that they too get compiled for the correct calling
 * convention.
 *
 * On non-Windows operating systems, this macro is defined as nothing. This
 * means that you can apply it to your code without worrying about
 * cross-platform compatibility.
 */

/** \ingroup libusb_desc
 * Device and/or Interface Class codes */
enum libusb_class_code {
	/** In the context of a \ref libusb_device_descriptor "device descriptor",
	 * this bDeviceClass value indicates that each interface specifies its
	 * own class information and all interfaces operate independently.
	 */
	LIBUSB_CLASS_PER_INTERFACE = 0x00,

	/** Audio class */
	LIBUSB_CLASS_AUDIO = 0x01,

	/** Communications class */
	LIBUSB_CLASS_COMM = 0x02,

	/** Human Interface Device class */
	LIBUSB_CLASS_HID = 0x03,

	/** Physical */
	LIBUSB_CLASS_PHYSICAL = 0x05,

	/** Image class */
	LIBUSB_CLASS_IMAGE = 0x06,
	LIBUSB_CLASS_PTP = 0x06, /* legacy name from libusb-0.1 usb.h */

	/** Printer class */
	LIBUSB_CLASS_PRINTER = 0x07,

	/** Mass storage class */
	LIBUSB_CLASS_MASS_STORAGE = 0x08,

	/** Hub class */
	LIBUSB_CLASS_HUB = 0x09,

	/** Data class */
	LIBUSB_CLASS_DATA = 0x0a,

	/** Smart Card */
	LIBUSB_CLASS_SMART_CARD = 0x0b,

	/** Content Security */
	LIBUSB_CLASS_CONTENT_SECURITY = 0x0d,

	/** Video */
	LIBUSB_CLASS_VIDEO = 0x0e,

	/** Personal Healthcare */
	LIBUSB_CLASS_PERSONAL_HEALTHCARE = 0x0f,

	/** Diagnostic Device */
	LIBUSB_CLASS_DIAGNOSTIC_DEVICE = 0xdc,

	/** Wireless class */
	LIBUSB_CLASS_WIRELESS = 0xe0,

	/** Miscellaneous class */
	LIBUSB_CLASS_MISCELLANEOUS = 0xef,

	/** Application class */
	LIBUSB_CLASS_APPLICATION = 0xfe,

	/** Class is vendor-specific */
	LIBUSB_CLASS_VENDOR_SPEC = 0xff
};

/** \ingroup libusb_desc
 * Descriptor types as defined by the USB specification. */
enum libusb_descriptor_type {
	/** Device descriptor. See libusb_device_descriptor. */
	LIBUSB_DT_DEVICE = 0x01,

	/** Configuration descriptor. See libusb_config_descriptor. */
	LIBUSB_DT_CONFIG = 0x02,

	/** String descriptor */
	LIBUSB_DT_STRING = 0x03,

	/** Interface descriptor. See libusb_interface_descriptor. */
	LIBUSB_DT_INTERFACE = 0x04,

	/** Endpoint descriptor. See libusb_endpoint_descriptor. */
	LIBUSB_DT_ENDPOINT = 0x05,

	/** BOS descriptor */
	LIBUSB_DT_BOS = 0x0f,

	/** Device Capability descriptor */
	LIBUSB_DT_DEVICE_CAPABILITY = 0x10,

	/** HID descriptor */
	LIBUSB_DT_HID = 0x21,

	/** HID report descriptor */
	LIBUSB_DT_REPORT = 0x22,

	/** Physical descriptor */
	LIBUSB_DT_PHYSICAL = 0x23,

	/** Hub descriptor */
	LIBUSB_DT_HUB = 0x29,

	/** SuperSpeed Hub descriptor */
	LIBUSB_DT_SUPERSPEED_HUB = 0x2a,

	/** SuperSpeed Endpoint Companion descriptor */
	LIBUSB_DT_SS_ENDPOINT_COMPANION = 0x30
};

/** \ingroup libusb_desc
 * Endpoint direction. Values for bit 7 of the
 * \ref libusb_endpoint_descriptor::bEndpointAddress "endpoint address" scheme.
 */
enum libusb_endpoint_direction {
	/** Out: host-to-device */
	LIBUSB_ENDPOINT_OUT = 0x00,

	/** In: device-to-host */
	LIBUSB_ENDPOINT_IN = 0x80
};

#define LIBUSB_TRANSFER_TYPE_MASK		0x03	/* in bmAttributes */

/** \ingroup libusb_desc
 * Endpoint transfer type. Values for bits 0:1 of the
 * \ref libusb_endpoint_descriptor::bmAttributes "endpoint attributes" field.
 */
enum libusb_endpoint_transfer_type {
	/** Control endpoint */
	LIBUSB_ENDPOINT_TRANSFER_TYPE_CONTROL = 0x0,

	/** Isochronous endpoint */
	LIBUSB_ENDPOINT_TRANSFER_TYPE_ISOCHRONOUS = 0x1,

	/** Bulk endpoint */
	LIBUSB_ENDPOINT_TRANSFER_TYPE_BULK = 0x2,

	/** Interrupt endpoint */
	LIBUSB_ENDPOINT_TRANSFER_TYPE_INTERRUPT = 0x3
};

/** \ingroup libusb_misc
 * Standard requests, as defined in table 9-5 of the USB 3.0 specifications */
enum libusb_standard_request {
	/** Request status of the specific recipient */
	LIBUSB_REQUEST_GET_STATUS = 0x00,

	/** Clear or disable a specific feature */
	LIBUSB_REQUEST_CLEAR_FEATURE = 0x01,

	/* 0x02 is reserved */

	/** Set or enable a specific feature */
	LIBUSB_REQUEST_SET_FEATURE = 0x03,

	/* 0x04 is reserved */

	/** Set device address for all future accesses */
	LIBUSB_REQUEST_SET_ADDRESS = 0x05,

	/** Get the specified descriptor */
	LIBUSB_REQUEST_GET_DESCRIPTOR = 0x06,

	/** Used to update existing descriptors or add new descriptors */
	LIBUSB_REQUEST_SET_DESCRIPTOR = 0x07,

	/** Get the current device configuration value */
	LIBUSB_REQUEST_GET_CONFIGURATION = 0x08,

	/** Set device configuration */
	LIBUSB_REQUEST_SET_CONFIGURATION = 0x09,

	/** Return the selected alternate setting for the specified interface */
	LIBUSB_REQUEST_GET_INTERFACE = 0x0a,

	/** Select an alternate interface for the specified interface */
	LIBUSB_REQUEST_SET_INTERFACE = 0x0b,

	/** Set then report an endpoint's synchronization frame */
	LIBUSB_REQUEST_SYNCH_FRAME = 0x0c,

	/** Sets both the U1 and U2 Exit Latency */
	LIBUSB_REQUEST_SET_SEL = 0x30,

	/** Delay from the time a host transmits a packet to the time it is
	  * received by the device. */
	LIBUSB_SET_ISOCH_DELAY = 0x31
};

/** \ingroup libusb_misc
 * Request type bits of the
 * \ref libusb_control_setup::bmRequestType "bmRequestType" field in control
 * transfers. */
enum libusb_request_type {
	/** Standard */
	LIBUSB_REQUEST_TYPE_STANDARD = (0x00 << 5),

	/** Class */
	LIBUSB_REQUEST_TYPE_CLASS = (0x01 << 5),

	/** Vendor */
	LIBUSB_REQUEST_TYPE_VENDOR = (0x02 << 5),

	/** Reserved */
	LIBUSB_REQUEST_TYPE_RESERVED = (0x03 << 5)
};

/** \ingroup libusb_misc
 * Recipient bits of the
 * \ref libusb_control_setup::bmRequestType "bmRequestType" field in control
 * transfers. Values 4 through 31 are reserved. */
enum libusb_request_recipient {
	/** Device */
	LIBUSB_RECIPIENT_DEVICE = 0x00,

	/** Interface */
	LIBUSB_RECIPIENT_INTERFACE = 0x01,

	/** Endpoint */
	LIBUSB_RECIPIENT_ENDPOINT = 0x02,

	/** Other */
	LIBUSB_RECIPIENT_OTHER = 0x03
};

/** \ingroup libusb_desc
 * Synchronization type for isochronous endpoints. Values for bits 2:3 of the
 * \ref libusb_endpoint_descriptor::bmAttributes "bmAttributes" field in
 * libusb_endpoint_descriptor.
 */
enum libusb_iso_sync_type {
	/** No synchronization */
	LIBUSB_ISO_SYNC_TYPE_NONE = 0x0,

	/** Asynchronous */
	LIBUSB_ISO_SYNC_TYPE_ASYNC = 0x1,

	/** Adaptive */
	LIBUSB_ISO_SYNC_TYPE_ADAPTIVE = 0x2,

	/** Synchronous */
	LIBUSB_ISO_SYNC_TYPE_SYNC = 0x3
};

/** \ingroup libusb_desc
 * Usage type for isochronous endpoints. Values for bits 4:5 of the
 * \ref libusb_endpoint_descriptor::bmAttributes "bmAttributes" field in
 * libusb_endpoint_descriptor.
 */
enum libusb_iso_usage_type {
	/** Data endpoint */
	LIBUSB_ISO_USAGE_TYPE_DATA = 0x0,

	/** Feedback endpoint */
	LIBUSB_ISO_USAGE_TYPE_FEEDBACK = 0x1,

	/** Implicit feedback Data endpoint */
	LIBUSB_ISO_USAGE_TYPE_IMPLICIT = 0x2
};

/** \ingroup libusb_desc
 * Supported speeds (wSpeedSupported) bitfield. Indicates what
 * speeds the device supports.
 */
enum libusb_supported_speed {
	/** Low speed operation supported (1.5MBit/s). */
	LIBUSB_LOW_SPEED_OPERATION = (1 << 0),

	/** Full speed operation supported (12MBit/s). */
	LIBUSB_FULL_SPEED_OPERATION = (1 << 1),

	/** High speed operation supported (480MBit/s). */
	LIBUSB_HIGH_SPEED_OPERATION = (1 << 2),

	/** Superspeed operation supported (5000MBit/s). */
	LIBUSB_SUPER_SPEED_OPERATION = (1 << 3)
};

/** \ingroup libusb_desc
 * Masks for the bits of the
 * \ref libusb_usb_2_0_extension_descriptor::bmAttributes "bmAttributes" field
 * of the USB 2.0 Extension descriptor.
 */
enum libusb_usb_2_0_extension_attributes {
	/** Supports Link Power Management (LPM) */
	LIBUSB_BM_LPM_SUPPORT = (1 << 1)
};

/** \ingroup libusb_desc
 * Masks for the bits of the
 * \ref libusb_ss_usb_device_capability_descriptor::bmAttributes "bmAttributes" field
 * field of the SuperSpeed USB Device Capability descriptor.
 */
enum libusb_ss_usb_device_capability_attributes {
	/** Supports Latency Tolerance Messages (LTM) */
	LIBUSB_BM_LTM_SUPPORT = (1 << 1)
};

/** \ingroup libusb_desc
 * USB capability types
 */
enum libusb_bos_type {
	/** Wireless USB device capability */
	LIBUSB_BT_WIRELESS_USB_DEVICE_CAPABILITY = 0x01,

	/** USB 2.0 extensions */
	LIBUSB_BT_USB_2_0_EXTENSION = 0x02,

	/** SuperSpeed USB device capability */
	LIBUSB_BT_SS_USB_DEVICE_CAPABILITY = 0x03,

	/** Container ID type */
	LIBUSB_BT_CONTAINER_ID = 0x04
};

/** \ingroup libusb_desc
 * A structure representing the standard USB device descriptor. This
 * descriptor is documented in section 9.6.1 of the USB 3.0 specification.
 * All multiple-byte fields are represented in host-endian format.
 */
struct libusb_device_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_DEVICE LIBUSB_DT_DEVICE in this
	 * context. */
	uint8_t  bDescriptorType;

	/** USB specification release number in binary-coded decimal. A value of
	 * 0x0200 indicates USB 2.0, 0x0110 indicates USB 1.1, etc. */
	uint16_t bcdUSB;

	/** USB-IF class code for the device. See \ref libusb_class_code. */
	uint8_t  bDeviceClass;

	/** USB-IF subclass code for the device, qualified by the bDeviceClass
	 * value */
	uint8_t  bDeviceSubClass;

	/** USB-IF protocol code for the device, qualified by the bDeviceClass and
	 * bDeviceSubClass values */
	uint8_t  bDeviceProtocol;

	/** Maximum packet size for endpoint 0 */
	uint8_t  bMaxPacketSize0;

	/** USB-IF vendor ID */
	uint16_t idVendor;

	/** USB-IF product ID */
	uint16_t idProduct;

	/** Device release number in binary-coded decimal */
	uint16_t bcdDevice;

	/** Index of string descriptor describing manufacturer */
	uint8_t  iManufacturer;

	/** Index of string descriptor describing product */
	uint8_t  iProduct;

	/** Index of string descriptor containing device serial number */
	uint8_t  iSerialNumber;

	/** Number of possible configurations */
	uint8_t  bNumConfigurations;
};

typedef struct libusb_device_descriptor libusb_device_descriptor;

/** \ingroup libusb_desc
 * A structure representing the standard USB endpoint descriptor. This
 * descriptor is documented in section 9.6.6 of the USB 3.0 specification.
 * All multiple-byte fields are represented in host-endian format.
 */
struct libusb_endpoint_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_ENDPOINT LIBUSB_DT_ENDPOINT in
	 * this context. */
	uint8_t  bDescriptorType;

	/** The address of the endpoint described by this descriptor. Bits 0:3 are
	 * the endpoint number. Bits 4:6 are reserved. Bit 7 indicates direction,
	 * see \ref libusb_endpoint_direction. */
	uint8_t  bEndpointAddress;

	/** Attributes which apply to the endpoint when it is configured using
	 * the bConfigurationValue. Bits 0:1 determine the transfer type and
	 * correspond to \ref libusb_endpoint_transfer_type. Bits 2:3 are only used
	 * for isochronous endpoints and correspond to \ref libusb_iso_sync_type.
	 * Bits 4:5 are also only used for isochronous endpoints and correspond to
	 * \ref libusb_iso_usage_type. Bits 6:7 are reserved. */
	uint8_t  bmAttributes;

	/** Maximum packet size this endpoint is capable of sending/receiving. */
	uint16_t wMaxPacketSize;

	/** Interval for polling endpoint for data transfers. */
	uint8_t  bInterval;

	/** For audio devices only: the rate at which synchronization feedback
	 * is provided. */
	uint8_t  bRefresh;

	/** For audio devices only: the address if the synch endpoint */
	uint8_t  bSynchAddress;

	/** Extra descriptors. If libusb encounters unknown endpoint descriptors,
	 * it will store them here, should you wish to parse them. */
	const unsigned char *extra;

	/** Length of the extra descriptors, in bytes. Must be non-negative. */
	int extra_length;
};

/** \ingroup libusb_desc
 * A structure representing the standard USB interface descriptor. This
 * descriptor is documented in section 9.6.5 of the USB 3.0 specification.
 * All multiple-byte fields are represented in host-endian format.
 */
struct libusb_interface_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_INTERFACE LIBUSB_DT_INTERFACE
	 * in this context. */
	uint8_t  bDescriptorType;

	/** Number of this interface */
	uint8_t  bInterfaceNumber;

	/** Value used to select this alternate setting for this interface */
	uint8_t  bAlternateSetting;

	/** Number of endpoints used by this interface (excluding the control
	 * endpoint). */
	uint8_t  bNumEndpoints;

	/** USB-IF class code for this interface. See \ref libusb_class_code. */
	uint8_t  bInterfaceClass;

	/** USB-IF subclass code for this interface, qualified by the
	 * bInterfaceClass value */
	uint8_t  bInterfaceSubClass;

	/** USB-IF protocol code for this interface, qualified by the
	 * bInterfaceClass and bInterfaceSubClass values */
	uint8_t  bInterfaceProtocol;

	/** Index of string descriptor describing this interface */
	uint8_t  iInterface;

	/** Array of endpoint descriptors. This length of this array is determined
	 * by the bNumEndpoints field. */
	const struct libusb_endpoint_descriptor *endpoint;

	/** Extra descriptors. If libusb encounters unknown interface descriptors,
	 * it will store them here, should you wish to parse them. */
	const unsigned char *extra;

	/** Length of the extra descriptors, in bytes. Must be non-negative. */
	int extra_length;
};

/** \ingroup libusb_desc
 * A collection of alternate settings for a particular USB interface.
 */
struct libusb_interface {
	/** Array of interface descriptors. The length of this array is determined
	 * by the num_altsetting field. */
	const struct libusb_interface_descriptor *altsetting;

	/** The number of alternate settings that belong to this interface.
	 * Must be non-negative. */
	int num_altsetting;
};

/** \ingroup libusb_desc
 * A structure representing the standard USB configuration descriptor. This
 * descriptor is documented in section 9.6.3 of the USB 3.0 specification.
 * All multiple-byte fields are represented in host-endian format.
 */
struct libusb_config_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_CONFIG LIBUSB_DT_CONFIG
	 * in this context. */
	uint8_t  bDescriptorType;

	/** Total length of data returned for this configuration */
	uint16_t wTotalLength;

	/** Number of interfaces supported by this configuration */
	uint8_t  bNumInterfaces;

	/** Identifier value for this configuration */
	uint8_t  bConfigurationValue;

	/** Index of string descriptor describing this configuration */
	uint8_t  iConfiguration;

	/** Configuration characteristics */
	uint8_t  bmAttributes;

	/** Maximum power consumption of the USB device from this bus in this
	 * configuration when the device is fully operation. Expressed in units
	 * of 2 mA when the device is operating in high-speed mode and in units
	 * of 8 mA when the device is operating in super-speed mode. */
	uint8_t  MaxPower;

	/** Array of interfaces supported by this configuration. The length of
	 * this array is determined by the bNumInterfaces field. */
	const struct libusb_interface *interface;

	/** Extra descriptors. If libusb encounters unknown configuration
	 * descriptors, it will store them here, should you wish to parse them. */
	const unsigned char *extra;

	/** Length of the extra descriptors, in bytes. Must be non-negative. */
	int extra_length;
};

typedef struct libusb_config_descriptor libusb_config_descriptor;

/** \ingroup libusb_desc
 * A structure representing the superspeed endpoint companion
 * descriptor. This descriptor is documented in section 9.6.7 of
 * the USB 3.0 specification. All multiple-byte fields are represented in
 * host-endian format.
 */
struct libusb_ss_endpoint_companion_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_SS_ENDPOINT_COMPANION in
	 * this context. */
	uint8_t  bDescriptorType;

	/** The maximum number of packets the endpoint can send or
	 *  receive as part of a burst. */
	uint8_t  bMaxBurst;

	/** In bulk EP: bits 4:0 represents the maximum number of
	 *  streams the EP supports. In isochronous EP: bits 1:0
	 *  represents the Mult - a zero based value that determines
	 *  the maximum number of packets within a service interval  */
	uint8_t  bmAttributes;

	/** The total number of bytes this EP will transfer every
	 *  service interval. Valid only for periodic EPs. */
	uint16_t wBytesPerInterval;
};

/** \ingroup libusb_desc
 * A generic representation of a BOS Device Capability descriptor. It is
 * advised to check bDevCapabilityType and call the matching
 * libusb_get_*_descriptor function to get a structure fully matching the type.
 */
struct libusb_bos_dev_capability_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_DEVICE_CAPABILITY
	 * LIBUSB_DT_DEVICE_CAPABILITY in this context. */
	uint8_t  bDescriptorType;

	/** Device Capability type */
	uint8_t  bDevCapabilityType;

	/** Device Capability data (bLength - 3 bytes) */
	uint8_t  dev_capability_data[0];
};

/** \ingroup libusb_desc
 * A structure representing the Binary Device Object Store (BOS) descriptor.
 * This descriptor is documented in section 9.6.2 of the USB 3.0 specification.
 * All multiple-byte fields are represented in host-endian format.
 */
struct libusb_bos_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_BOS LIBUSB_DT_BOS
	 * in this context. */
	uint8_t  bDescriptorType;

	/** Length of this descriptor and all of its sub descriptors */
	uint16_t wTotalLength;

	/** The number of separate device capability descriptors in
	 * the BOS */
	uint8_t  bNumDeviceCaps;

	/** bNumDeviceCap Device Capability Descriptors */
	struct libusb_bos_dev_capability_descriptor *dev_capability[ZERO_SIZED_ARRAY];
};

/** \ingroup libusb_desc
 * A structure representing the USB 2.0 Extension descriptor
 * This descriptor is documented in section 9.6.2.1 of the USB 3.0 specification.
 * All multiple-byte fields are represented in host-endian format.
 */
struct libusb_usb_2_0_extension_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_DEVICE_CAPABILITY
	 * LIBUSB_DT_DEVICE_CAPABILITY in this context. */
	uint8_t  bDescriptorType;

	/** Capability type. Will have value
	 * \ref libusb_capability_type::LIBUSB_BT_USB_2_0_EXTENSION
	 * LIBUSB_BT_USB_2_0_EXTENSION in this context. */
	uint8_t  bDevCapabilityType;

	/** Bitmap encoding of supported device level features.
	 * A value of one in a bit location indicates a feature is
	 * supported; a value of zero indicates it is not supported.
	 * See \ref libusb_usb_2_0_extension_attributes. */
	uint32_t bmAttributes;
};

/** \ingroup libusb_desc
 * A structure representing the SuperSpeed USB Device Capability descriptor
 * This descriptor is documented in section 9.6.2.2 of the USB 3.0 specification.
 * All multiple-byte fields are represented in host-endian format.
 */
struct libusb_ss_usb_device_capability_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_DEVICE_CAPABILITY
	 * LIBUSB_DT_DEVICE_CAPABILITY in this context. */
	uint8_t  bDescriptorType;

	/** Capability type. Will have value
	 * \ref libusb_capability_type::LIBUSB_BT_SS_USB_DEVICE_CAPABILITY
	 * LIBUSB_BT_SS_USB_DEVICE_CAPABILITY in this context. */
	uint8_t  bDevCapabilityType;

	/** Bitmap encoding of supported device level features.
	 * A value of one in a bit location indicates a feature is
	 * supported; a value of zero indicates it is not supported.
	 * See \ref libusb_ss_usb_device_capability_attributes. */
	uint8_t  bmAttributes;

	/** Bitmap encoding of the speed supported by this device when
	 * operating in SuperSpeed mode. See \ref libusb_supported_speed. */
	uint16_t wSpeedSupported;

	/** The lowest speed at which all the functionality supported
	 * by the device is available to the user. For example if the
	 * device supports all its functionality when connected at
	 * full speed and above then it sets this value to 1. */
	uint8_t  bFunctionalitySupport;

	/** U1 Device Exit Latency. */
	uint8_t  bU1DevExitLat;

	/** U2 Device Exit Latency. */
	uint16_t bU2DevExitLat;
};

/** \ingroup libusb_desc
 * A structure representing the Container ID descriptor.
 * This descriptor is documented in section 9.6.2.3 of the USB 3.0 specification.
 * All multiple-byte fields, except UUIDs, are represented in host-endian format.
 */
struct libusb_container_id_descriptor {
	/** Size of this descriptor (in bytes) */
	uint8_t  bLength;

	/** Descriptor type. Will have value
	 * \ref libusb_descriptor_type::LIBUSB_DT_DEVICE_CAPABILITY
	 * LIBUSB_DT_DEVICE_CAPABILITY in this context. */
	uint8_t  bDescriptorType;

	/** Capability type. Will have value
	 * \ref libusb_capability_type::LIBUSB_BT_CONTAINER_ID
	 * LIBUSB_BT_CONTAINER_ID in this context. */
	uint8_t  bDevCapabilityType;

	/** Reserved field */
	uint8_t  bReserved;

	/** 128 bit UUID */
	uint8_t  ContainerID[16];
};

/* libusb */

struct libusb_context;
struct libusb_device;
struct libusb_device_handle;

/** \ingroup libusb_lib
 * Structure providing the version of the libusb runtime
 */
struct libusb_version {
	/** Library major version. */
	const uint16_t major;

	/** Library minor version. */
	const uint16_t minor;

	/** Library micro version. */
	const uint16_t micro;

	/** Library nano version. */
	const uint16_t nano;

	/** Library release candidate suffix string, e.g. "-rc4". */
	const char *rc;

	/** For ABI compatibility only. */
	const char *describe;
};

/** \ingroup libusb_lib
 * Structure representing a libusb session. The concept of individual libusb
 * sessions allows for your program to use two libraries (or dynamically
 * load two modules) which both independently use libusb. This will prevent
 * interference between the individual libusb users - for example
 * libusb_set_option() will not affect the other user of the library, and
 * libusb_exit() will not destroy resources that the other user is still
 * using.
 *
 * Sessions are created by libusb_init() and destroyed through libusb_exit().
 * If your application is guaranteed to only ever include a single libusb
 * user (i.e. you), you do not have to worry about contexts: pass NULL in
 * every function call where a context is required. The default context
 * will be used.
 *
 * For more information, see \ref libusb_contexts.
 */
typedef struct libusb_context libusb_context;

/** \ingroup libusb_dev
 * Structure representing a USB device detected on the system. This is an
 * opaque type for which you are only ever provided with a pointer, usually
 * originating from libusb_get_device_list().
 *
 * Certain operations can be performed on a device, but in order to do any
 * I/O you will have to first obtain a device handle using libusb_open().
 *
 * Devices are reference counted with libusb_ref_device() and
 * libusb_unref_device(), and are freed when the reference count reaches 0.
 * New devices presented by libusb_get_device_list() have a reference count of
 * 1, and libusb_free_device_list() can optionally decrease the reference count
 * on all devices in the list. libusb_open() adds another reference which is
 * later destroyed by libusb_close().
 */
typedef struct libusb_device libusb_device;


/** \ingroup libusb_dev
 * Structure representing a handle on a USB device. This is an opaque type for
 * which you are only ever provided with a pointer, usually originating from
 * libusb_open().
 *
 * A device handle is used to perform I/O and other operations. When finished
 * with a device handle, you should call libusb_close().
 */
typedef struct libusb_device_handle libusb_device_handle;

/** \ingroup libusb_dev
 * Speed codes. Indicates the speed at which the device is operating.
 */
enum libusb_speed {
	/** The OS doesn't report or know the device speed. */
	LIBUSB_SPEED_UNKNOWN = 0,

	/** The device is operating at low speed (1.5MBit/s). */
	LIBUSB_SPEED_LOW = 1,

	/** The device is operating at full speed (12MBit/s). */
	LIBUSB_SPEED_FULL = 2,

	/** The device is operating at high speed (480MBit/s). */
	LIBUSB_SPEED_HIGH = 3,

	/** The device is operating at super speed (5000MBit/s). */
	LIBUSB_SPEED_SUPER = 4,

	/** The device is operating at super speed plus (10000MBit/s). */
	LIBUSB_SPEED_SUPER_PLUS = 5
};

/** \ingroup libusb_misc
 * Error codes. Most libusb functions return 0 on success or one of these
 * codes on failure.
 * You can call libusb_error_name() to retrieve a string representation of an
 * error code or libusb_strerror() to get an end-user suitable description of
 * an error code.
 */
enum libusb_error {
	/** Success (no error) */
	LIBUSB_SUCCESS = 0,

	/** Input/output error */
	LIBUSB_ERROR_IO = -1,

	/** Invalid parameter */
	LIBUSB_ERROR_INVALID_PARAM = -2,

	/** Access denied (insufficient permissions) */
	LIBUSB_ERROR_ACCESS = -3,

	/** No such device (it may have been disconnected) */
	LIBUSB_ERROR_NO_DEVICE = -4,

	/** Entity not found */
	LIBUSB_ERROR_NOT_FOUND = -5,

	/** Resource busy */
	LIBUSB_ERROR_BUSY = -6,

	/** Operation timed out */
	LIBUSB_ERROR_TIMEOUT = -7,

	/** Overflow */
	LIBUSB_ERROR_OVERFLOW = -8,

	/** Pipe error */
	LIBUSB_ERROR_PIPE = -9,

	/** System call interrupted (perhaps due to signal) */
	LIBUSB_ERROR_INTERRUPTED = -10,

	/** Insufficient memory */
	LIBUSB_ERROR_NO_MEM = -11,

	/** Operation not supported or unimplemented on this platform */
	LIBUSB_ERROR_NOT_SUPPORTED = -12,

	/* NB: Remember to update LIBUSB_ERROR_COUNT below as well as the
	   message strings in strerror.c when adding new error codes here. */

	/** Other error */
	LIBUSB_ERROR_OTHER = -99
};

/** \ingroup libusb_asyncio
 * Transfer type */
enum libusb_transfer_type {
	/** Control transfer */
	LIBUSB_TRANSFER_TYPE_CONTROL = 0U,

	/** Isochronous transfer */
	LIBUSB_TRANSFER_TYPE_ISOCHRONOUS = 1U,

	/** Bulk transfer */
	LIBUSB_TRANSFER_TYPE_BULK = 2U,

	/** Interrupt transfer */
	LIBUSB_TRANSFER_TYPE_INTERRUPT = 3U,

	/** Bulk stream transfer */
	LIBUSB_TRANSFER_TYPE_BULK_STREAM = 4U
};

/** \ingroup libusb_asyncio
 * Transfer status codes */
enum libusb_transfer_status {
	/** Transfer completed without error. Note that this does not indicate
	 * that the entire amount of requested data was transferred. */
	LIBUSB_TRANSFER_COMPLETED,

	/** Transfer failed */
	LIBUSB_TRANSFER_ERROR,

	/** Transfer timed out */
	LIBUSB_TRANSFER_TIMED_OUT,

	/** Transfer was cancelled */
	LIBUSB_TRANSFER_CANCELLED,

	/** For bulk/interrupt endpoints: halt condition detected (endpoint
	 * stalled). For control endpoints: control request not supported. */
	LIBUSB_TRANSFER_STALL,

	/** Device was disconnected */
	LIBUSB_TRANSFER_NO_DEVICE,

	/** Device sent more data than requested */
	LIBUSB_TRANSFER_OVERFLOW

	/* NB! Remember to update libusb_error_name()
	   when adding new status codes here. */
};

/** \ingroup libusb_asyncio
 * libusb_transfer.flags values */
enum libusb_transfer_flags {
	/** Report short frames as errors */
	LIBUSB_TRANSFER_SHORT_NOT_OK = (1U << 0),

	/** Automatically free() transfer buffer during libusb_free_transfer().
	 * Note that buffers allocated with libusb_dev_mem_alloc() should not
	 * be attempted freed in this way, since free() is not an appropriate
	 * way to release such memory. */
	LIBUSB_TRANSFER_FREE_BUFFER = (1U << 1),

	/** Automatically call libusb_free_transfer() after callback returns.
	 * If this flag is set, it is illegal to call libusb_free_transfer()
	 * from your transfer callback, as this will result in a double-free
	 * when this flag is acted upon. */
	LIBUSB_TRANSFER_FREE_TRANSFER = (1U << 2),

	/** Terminate transfers that are a multiple of the endpoint's
	 * wMaxPacketSize with an extra zero length packet. This is useful
	 * when a device protocol mandates that each logical request is
	 * terminated by an incomplete packet (i.e. the logical requests are
	 * not separated by other means).
	 *
	 * This flag only affects host-to-device transfers to bulk and interrupt
	 * endpoints. In other situations, it is ignored.
	 *
	 * This flag only affects transfers with a length that is a multiple of
	 * the endpoint's wMaxPacketSize. On transfers of other lengths, this
	 * flag has no effect. Therefore, if you are working with a device that
	 * needs a ZLP whenever the end of the logical request falls on a packet
	 * boundary, then it is sensible to set this flag on <em>every</em>
	 * transfer (you do not have to worry about only setting it on transfers
	 * that end on the boundary).
	 *
	 * This flag is currently only supported on Linux.
	 * On other systems, libusb_submit_transfer() will return
	 * LIBUSB_ERROR_NOT_SUPPORTED for every transfer where this flag is set.
	 *
	 * Available since libusb-1.0.9.
	 */
	LIBUSB_TRANSFER_ADD_ZERO_PACKET = (1U << 3)
};

/** \ingroup libusb_asyncio
 * Isochronous packet descriptor. */
struct libusb_iso_packet_descriptor {
	/** Length of data to request in this packet */
	unsigned int length;

	/** Amount of data that was actually transferred */
	unsigned int actual_length;

	/** Status code for this packet */
	enum libusb_transfer_status status;
};

struct libusb_transfer;

/** \ingroup libusb_asyncio
 * Asynchronous transfer callback function type. When submitting asynchronous
 * transfers, you pass a pointer to a callback function of this type via the
 * \ref libusb_transfer::callback "callback" member of the libusb_transfer
 * structure. libusb will call this function later, when the transfer has
 * completed or failed. See \ref libusb_asyncio for more information.
 * \param transfer The libusb_transfer struct the callback function is being
 * notified about.
 */
typedef void (*libusb_transfer_cb_fn)(struct libusb_transfer *transfer);

/** \ingroup libusb_asyncio
 * The generic USB transfer structure. The user populates this structure and
 * then submits it in order to request a transfer. After the transfer has
 * completed, the library populates the transfer with the results and passes
 * it back to the user.
 */
struct libusb_transfer {
	/** Handle of the device that this transfer will be submitted to */
	libusb_device_handle *dev_handle;

	/** A bitwise OR combination of \ref libusb_transfer_flags. */
	uint8_t flags;

	/** Address of the endpoint where this transfer will be sent. */
	unsigned char endpoint;

	/** Type of the transfer from \ref libusb_transfer_type */
	unsigned char type;

	/** Timeout for this transfer in milliseconds. A value of 0 indicates no
	 * timeout. */
	unsigned int timeout;

	/** The status of the transfer. Read-only, and only for use within
	 * transfer callback function.
	 *
	 * If this is an isochronous transfer, this field may read COMPLETED even
	 * if there were errors in the frames. Use the
	 * \ref libusb_iso_packet_descriptor::status "status" field in each packet
	 * to determine if errors occurred. */
	enum libusb_transfer_status status;

	/** Length of the data buffer. Must be non-negative. */
	int length;

	/** Actual length of data that was transferred. Read-only, and only for
	 * use within transfer callback function. Not valid for isochronous
	 * endpoint transfers. */
	int actual_length;

	/** Callback function. This will be invoked when the transfer completes,
	 * fails, or is cancelled. */
	libusb_transfer_cb_fn callback;

	/** User context data. Useful for associating specific data to a transfer
	 * that can be accessed from within the callback function.
	 *
	 * This field may be set manually or is taken as the `user_data` parameter
	 * of the following functions:
	 * - libusb_fill_bulk_transfer()
	 * - libusb_fill_bulk_stream_transfer()
	 * - libusb_fill_control_transfer()
	 * - libusb_fill_interrupt_transfer()
	 * - libusb_fill_iso_transfer() */
	void *user_data;

	/** Data buffer */
	unsigned char *buffer;

	/** Number of isochronous packets. Only used for I/O with isochronous
	 * endpoints. Must be non-negative. */
	int num_iso_packets;

	/** Isochronous packet descriptors, for isochronous transfers only. */
	struct libusb_iso_packet_descriptor iso_packet_desc[ZERO_SIZED_ARRAY];
};

/** \ingroup libusb_misc
 * Capabilities supported by an instance of libusb on the current running
 * platform. Test if the loaded library supports a given capability by calling
 * \ref libusb_has_capability().
 */
enum libusb_capability {
	/** The libusb_has_capability() API is available. */
	LIBUSB_CAP_HAS_CAPABILITY = 0x0000U,

	/** Hotplug support is available on this platform. */
	LIBUSB_CAP_HAS_HOTPLUG = 0x0001U,

	/** The library can access HID devices without requiring user intervention.
	 * Note that before being able to actually access an HID device, you may
	 * still have to call additional libusb functions such as
	 * \ref libusb_detach_kernel_driver(). */
	LIBUSB_CAP_HAS_HID_ACCESS = 0x0100U,

	/** The library supports detaching of the default USB driver, using
	 * \ref libusb_detach_kernel_driver(), if one is set by the OS kernel */
	LIBUSB_CAP_SUPPORTS_DETACH_KERNEL_DRIVER = 0x0101U
};

/** \ingroup libusb_lib
 *  Log message levels.
 */
enum libusb_log_level {
	/** (0) : No messages ever emitted by the library (default) */
	LIBUSB_LOG_LEVEL_NONE = 0,

	/** (1) : Error messages are emitted */
	LIBUSB_LOG_LEVEL_ERROR = 1,

	/** (2) : Warning and error messages are emitted */
	LIBUSB_LOG_LEVEL_WARNING = 2,

	/** (3) : Informational, warning and error messages are emitted */
	LIBUSB_LOG_LEVEL_INFO = 3,

	/** (4) : All messages are emitted */
	LIBUSB_LOG_LEVEL_DEBUG = 4
};

/** \ingroup libusb_lib
 *  Log callback mode.
 * \see libusb_set_log_cb()
 */
enum libusb_log_cb_mode {
	/** Callback function handling all log messages. */
	LIBUSB_LOG_CB_GLOBAL = (1 << 0),

	/** Callback function handling context related log messages. */
	LIBUSB_LOG_CB_CONTEXT = (1 << 1)
};

int libusb_init(libusb_context **ctx);
void libusb_exit(libusb_context *ctx);
void libusb_set_debug(libusb_context *ctx, int level);
const struct libusb_version * libusb_get_version(void);
int libusb_has_capability(uint32_t capability);
const char * libusb_error_name(int errcode);
int libusb_setlocale(const char *locale);
const char * libusb_strerror(int errcode);

ssize_t libusb_get_device_list(libusb_context *ctx,
	libusb_device ***list);
void libusb_free_device_list(libusb_device **list,
	int unref_devices);
libusb_device * libusb_ref_device(libusb_device *dev);
void libusb_unref_device(libusb_device *dev);

int libusb_get_configuration(libusb_device_handle *dev,
	int *config);
int libusb_get_device_descriptor(libusb_device *dev,
	struct libusb_device_descriptor *desc);
int libusb_get_active_config_descriptor(libusb_device *dev,
	struct libusb_config_descriptor **config);
int libusb_get_config_descriptor(libusb_device *dev,
	uint8_t config_index, struct libusb_config_descriptor **config);
int libusb_get_config_descriptor_by_value(libusb_device *dev,
	uint8_t bConfigurationValue, struct libusb_config_descriptor **config);
void libusb_free_config_descriptor(
	struct libusb_config_descriptor *config);
int libusb_get_ss_endpoint_companion_descriptor(
	libusb_context *ctx,
	const struct libusb_endpoint_descriptor *endpoint,
	struct libusb_ss_endpoint_companion_descriptor **ep_comp);
void libusb_free_ss_endpoint_companion_descriptor(
	struct libusb_ss_endpoint_companion_descriptor *ep_comp);
int libusb_get_bos_descriptor(libusb_device_handle *dev_handle,
	struct libusb_bos_descriptor **bos);
void libusb_free_bos_descriptor(struct libusb_bos_descriptor *bos);
int libusb_get_usb_2_0_extension_descriptor(
	libusb_context *ctx,
	struct libusb_bos_dev_capability_descriptor *dev_cap,
	struct libusb_usb_2_0_extension_descriptor **usb_2_0_extension);
void libusb_free_usb_2_0_extension_descriptor(
	struct libusb_usb_2_0_extension_descriptor *usb_2_0_extension);
int libusb_get_ss_usb_device_capability_descriptor(
	libusb_context *ctx,
	struct libusb_bos_dev_capability_descriptor *dev_cap,
	struct libusb_ss_usb_device_capability_descriptor **ss_usb_device_cap);
void libusb_free_ss_usb_device_capability_descriptor(
	struct libusb_ss_usb_device_capability_descriptor *ss_usb_device_cap);
int libusb_get_container_id_descriptor(libusb_context *ctx,
	struct libusb_bos_dev_capability_descriptor *dev_cap,
	struct libusb_container_id_descriptor **container_id);
void libusb_free_container_id_descriptor(
	struct libusb_container_id_descriptor *container_id);
uint8_t libusb_get_bus_number(libusb_device *dev);
uint8_t libusb_get_port_number(libusb_device *dev);
int libusb_get_port_numbers(libusb_device *dev, uint8_t *port_numbers, int port_numbers_len);
int libusb_get_port_path(libusb_context *ctx, libusb_device *dev, uint8_t *path, uint8_t path_length);
libusb_device * libusb_get_parent(libusb_device *dev);
uint8_t libusb_get_device_address(libusb_device *dev);
int libusb_get_device_speed(libusb_device *dev);
int libusb_get_max_packet_size(libusb_device *dev,
	unsigned char endpoint);
int libusb_get_max_iso_packet_size(libusb_device *dev,
	unsigned char endpoint);

int libusb_wrap_sys_device(libusb_context *ctx, intptr_t sys_dev, libusb_device_handle **dev_handle);
int libusb_open(libusb_device *dev, libusb_device_handle **dev_handle);
void libusb_close(libusb_device_handle *dev_handle);
libusb_device * libusb_get_device(libusb_device_handle *dev_handle);

int libusb_set_configuration(libusb_device_handle *dev_handle,
	int configuration);
int libusb_claim_interface(libusb_device_handle *dev_handle,
	int interface_number);
int libusb_release_interface(libusb_device_handle *dev_handle,
	int interface_number);

libusb_device_handle * libusb_open_device_with_vid_pid(
	libusb_context *ctx, uint16_t vendor_id, uint16_t product_id);

int libusb_set_interface_alt_setting(libusb_device_handle *dev_handle,
	int interface_number, int alternate_setting);
int libusb_clear_halt(libusb_device_handle *dev_handle,
	unsigned char endpoint);
int libusb_reset_device(libusb_device_handle *dev_handle);

int libusb_alloc_streams(libusb_device_handle *dev_handle,
	uint32_t num_streams, unsigned char *endpoints, int num_endpoints);
int libusb_free_streams(libusb_device_handle *dev_handle,
	unsigned char *endpoints, int num_endpoints);

unsigned char * libusb_dev_mem_alloc(libusb_device_handle *dev_handle,
	size_t length);
int libusb_dev_mem_free(libusb_device_handle *dev_handle,
	unsigned char *buffer, size_t length);

int libusb_kernel_driver_active(libusb_device_handle *dev_handle,
	int interface_number);
int libusb_detach_kernel_driver(libusb_device_handle *dev_handle,
	int interface_number);
int libusb_attach_kernel_driver(libusb_device_handle *dev_handle,
	int interface_number);
int libusb_set_auto_detach_kernel_driver(
	libusb_device_handle *dev_handle, int enable);

struct libusb_transfer * libusb_alloc_transfer(int iso_packets);
int libusb_submit_transfer(struct libusb_transfer *transfer);
int libusb_cancel_transfer(struct libusb_transfer *transfer);
void libusb_free_transfer(struct libusb_transfer *transfer);
void libusb_transfer_set_stream_id(
	struct libusb_transfer *transfer, uint32_t stream_id);
uint32_t libusb_transfer_get_stream_id(
	struct libusb_transfer *transfer);

/* sync I/O */

int libusb_control_transfer(libusb_device_handle *dev_handle,
	uint8_t request_type, uint8_t bRequest, uint16_t wValue, uint16_t wIndex,
	unsigned char *data, uint16_t wLength, unsigned int timeout);

int libusb_bulk_transfer(libusb_device_handle *dev_handle,
	unsigned char endpoint, unsigned char *data, int length,
	int *actual_length, unsigned int timeout);

int libusb_interrupt_transfer(libusb_device_handle *dev_handle,
	unsigned char endpoint, unsigned char *data, int length,
	int *actual_length, unsigned int timeout);

int libusb_get_string_descriptor_ascii(libusb_device_handle *dev_handle,
	uint8_t desc_index, unsigned char *data, int length);

/* polling and timeouts */

int libusb_try_lock_events(libusb_context *ctx);
void libusb_lock_events(libusb_context *ctx);
void libusb_unlock_events(libusb_context *ctx);
int libusb_event_handling_ok(libusb_context *ctx);
int libusb_event_handler_active(libusb_context *ctx);
void libusb_interrupt_event_handler(libusb_context *ctx);
void libusb_lock_event_waiters(libusb_context *ctx);
void libusb_unlock_event_waiters(libusb_context *ctx);
int libusb_wait_for_event(libusb_context *ctx, struct timeval *tv);

int libusb_handle_events_timeout(libusb_context *ctx,
	struct timeval *tv);
int libusb_handle_events_timeout_completed(libusb_context *ctx,
	struct timeval *tv, int *completed);
int libusb_handle_events(libusb_context *ctx);
int libusb_handle_events_completed(libusb_context *ctx, int *completed);
int libusb_handle_events_locked(libusb_context *ctx,
	struct timeval *tv);
int libusb_pollfds_handle_timeouts(libusb_context *ctx);
int libusb_get_next_timeout(libusb_context *ctx,
	struct timeval *tv);

/** \ingroup libusb_poll
 * File descriptor for polling
 */
struct libusb_pollfd {
	/** Numeric file descriptor */
	int fd;

	/** Event flags to poll for from <poll.h>. POLLIN indicates that you
	 * should monitor this file descriptor for becoming ready to read from,
	 * and POLLOUT indicates that you should monitor this file descriptor for
	 * nonblocking write readiness. */
	short events;
};

/** \ingroup libusb_poll
 * Callback function, invoked when a new file descriptor should be added
 * to the set of file descriptors monitored for events.
 * \param fd the new file descriptor
 * \param events events to monitor for, see \ref libusb_pollfd for a
 * description
 * \param user_data User data pointer specified in
 * libusb_set_pollfd_notifiers() call
 * \see libusb_set_pollfd_notifiers()
 */
typedef void (*libusb_pollfd_added_cb)(int fd, short events,
	void *user_data);

/** \ingroup libusb_poll
 * Callback function, invoked when a file descriptor should be removed from
 * the set of file descriptors being monitored for events. After returning
 * from this callback, do not use that file descriptor again.
 * \param fd the file descriptor to stop monitoring
 * \param user_data User data pointer specified in
 * libusb_set_pollfd_notifiers() call
 * \see libusb_set_pollfd_notifiers()
 */
typedef void (*libusb_pollfd_removed_cb)(int fd, void *user_data);

const struct libusb_pollfd ** libusb_get_pollfds(
	libusb_context *ctx);
void libusb_free_pollfds(const struct libusb_pollfd **pollfds);
void libusb_set_pollfd_notifiers(libusb_context *ctx,
	libusb_pollfd_added_cb added_cb, libusb_pollfd_removed_cb removed_cb,
	void *user_data);

/** \ingroup libusb_hotplug
 * Callback handle.
 *
 * Callbacks handles are generated by libusb_hotplug_register_callback()
 * and can be used to deregister callbacks. Callback handles are unique
 * per libusb_context and it is safe to call libusb_hotplug_deregister_callback()
 * on an already deregistered callback.
 *
 * Since version 1.0.16, \ref LIBUSB_API_VERSION >= 0x01000102
 *
 * For more information, see \ref libusb_hotplug.
 */
typedef int libusb_hotplug_callback_handle;

/** \ingroup libusb_hotplug
 *
 * Since version 1.0.16, \ref LIBUSB_API_VERSION >= 0x01000102
 *
 * Hotplug events */
typedef enum {
	/** A device has been plugged in and is ready to use */
	LIBUSB_HOTPLUG_EVENT_DEVICE_ARRIVED = (1 << 0),

	/** A device has left and is no longer available.
	 * It is the user's responsibility to call libusb_close on any handle associated with a disconnected device.
	 * It is safe to call libusb_get_device_descriptor on a device that has left */
	LIBUSB_HOTPLUG_EVENT_DEVICE_LEFT = (1 << 1)
} libusb_hotplug_event;

/** \ingroup libusb_hotplug
 *
 * Since version 1.0.16, \ref LIBUSB_API_VERSION >= 0x01000102
 *
 * Hotplug flags */
typedef enum {
	/** Arm the callback and fire it for all matching currently attached devices. */
	LIBUSB_HOTPLUG_ENUMERATE = (1 << 0)
} libusb_hotplug_flag;

/** \ingroup libusb_hotplug
 * Deregisters a hotplug callback.
 *
 * Deregister a callback from a libusb_context. This function is safe to call from within
 * a hotplug callback.
 *
 * Since version 1.0.16, \ref LIBUSB_API_VERSION >= 0x01000102
 *
 * \param[in] ctx context this callback is registered with
 * \param[in] callback_handle the handle of the callback to deregister
 */
void libusb_hotplug_deregister_callback(libusb_context *ctx,
	libusb_hotplug_callback_handle callback_handle);

/** \ingroup libusb_hotplug
 * Gets the user_data associated with a hotplug callback.
 *
 * Since version v1.0.24 \ref LIBUSB_API_VERSION >= 0x01000108
 *
 * \param[in] ctx context this callback is registered with
 * \param[in] callback_handle the handle of the callback to get the user_data of
 */
void * libusb_hotplug_get_user_data(libusb_context *ctx,
	libusb_hotplug_callback_handle callback_handle);

/** \ingroup libusb_lib
 * Available option values for libusb_set_option().
 */
enum libusb_option {
	/** Set the log message verbosity.
	 *
	 * The default level is LIBUSB_LOG_LEVEL_NONE, which means no messages are ever
	 * printed. If you choose to increase the message verbosity level, ensure
	 * that your application does not close the stderr file descriptor.
	 *
	 * You are advised to use level LIBUSB_LOG_LEVEL_WARNING. libusb is conservative
	 * with its message logging and most of the time, will only log messages that
	 * explain error conditions and other oddities. This will help you debug
	 * your software.
	 *
	 * If the LIBUSB_DEBUG environment variable was set when libusb was
	 * initialized, this function does nothing: the message verbosity is fixed
	 * to the value in the environment variable.
	 *
	 * If libusb was compiled without any message logging, this function does
	 * nothing: you'll never get any messages.
	 *
	 * If libusb was compiled with verbose debug message logging, this function
	 * does nothing: you'll always get messages from all levels.
	 */
	LIBUSB_OPTION_LOG_LEVEL = 0,

	/** Use the UsbDk backend for a specific context, if available.
	 *
	 * This option should be set immediately after calling libusb_init(), otherwise
	 * unspecified behavior may occur.
	 *
	 * Only valid on Windows.
	 */
	LIBUSB_OPTION_USE_USBDK = 1,

	/** Set libusb has weak authority. With this option, libusb will skip
	 * scan devices in libusb_init.
	 *
	 * This option should be set before calling libusb_init(), otherwise
	 * libusb_init will failed. Normally libusb_wrap_sys_device need set
	 * this option.
	 *
	 * Only valid on Linux-based operating system, such as Android.
	 */
	LIBUSB_OPTION_WEAK_AUTHORITY = 2
};

int libusb_set_option(libusb_context *ctx, enum libusb_option option, ...);
