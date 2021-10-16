# What is nfc-for-php?

This library is a toy for me. You can read NFC with libnfc written in PHP.

# Requirements
- PHP 7.4+
- PHP FFI
- libnfc 1.8.0 (_if you use NFC reader for PaSoRi RC-S330_)
- libusb 1.0.24 (_if you use NFC reader for PaSoRi RC-S380_)

# Tested

- OS
  - Mac Big Sur
  - RaspberryPi (Raspbian)
- FeliCa
  - PASMO
  - Suica
  - KONAMI e-AMUSEMENT pass
  - Pixel5
- PaSoRi RC-S330
  - You **cannot** touch Apple Wallet on iOS
  - https://www.amazon.co.jp/dp/B001MVPD8U/
- PaSoRi RC-S380 (experimental)
  - Supported only to a FeliCa
  - You can touch Apple Wallet on iOS
  - https://www.amazon.co.jp/dp/B00948CGAG/

<img src="images/pasori.jpg" />

# Quick start

## Mac
### RC-S330
1. Install libnfc

```
$ brew install libnfc
```

2. Install this library

```
$ composer require m3m0r7/nfc-for-php
```

3. Connect NFC Device into your machine.
4. Run example code

```
$ cd /path/to/nfc-for-php
$ php examples/nfc-poll-simple.php
```

5. Put your NFC

<img src="images/felica.png">

6. You can get output.

<img src="images/example.jpg">

### RC-S380

1. Install libnfc

```
$ brew install libusb
```

2. Install this library

```
$ composer require m3m0r7/nfc-for-php
```

3. Connect NFC Device into your machine.
4. Run example code

```
$ cd /path/to/nfc-for-php
$ php examples/rcs380-nfc-poll-simple.php
```

5. Put your NFC
6. You can get output.

# NFC binary

We're providing NFC binary in `vendor/bin`.
And you can use 3 commands `start`, `ls` and `version`


## start

Start to listen NFC reader.

```
./vendor/bin/nfc start
```

You can change the driver if you want to use other driver.

```
./vendor/bin/nfc start -D DriverName
```

You can specify hook events in CLI command.

```
./vendor/bin/nfc start -E /path/to/events.php
```

For example, you can use example file.

```
./vendor/bin/nfc start -E ./vendor/m3m0r7/nfc-for-php/examples/event-manager.php
```


And you want to more details, please run below command.

```
./vendor/bin/nfc start --help
```

## ls

Show available devices.

```
./vendor/bin/nfc ls
```

You can change the driver if you want to use other driver.

```
./vendor/bin/nfc ls -D DriverName
```

And you want to more details, please run below command.

```
./vendor/bin/nfc ls --help
```

## version

Show PHP driver version.

```
./vendor/bin/nfc version
```

You can change the driver if you want to use other driver.

```
./vendor/bin/nfc version -D DriverName
```

And you want to more details, please run below command.

```
./vendor/bin/nfc version --help
```

# How to use

- See examples

# Troubleshooting

## Q. How to recognize a device?
A. You must have granted permission to user.
I already test on RaspberryPI (Raspbian), it should grant to access permission to `/dev/*` or using super user (e.g., the root user).
If you did not have permissions, cannot recognize a device on your environment and the libusb return an error `LIBUSB_ERROR_ACCESS`.

## Q. Cannot start second time or later on macOS
A. You must reconnect the connected USB plug because libnfc/libusb return an invalid packet in the macOS when second time or later.
I don't know how to fix it.

# License
- MIT
