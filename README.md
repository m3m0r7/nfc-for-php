# What is nfc-for-php?

This library is a toy for me. You can read NFC with libnfc written in PHP.

# Requirements
- PHP 7.4+
- PHP FFI
- libnfc (_if you use NFC reader for RC-S330_)
- libusb (_if you use NFC reader for RC-S380_)

# Tested

- Mac Big Sur
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

<img src="images/pasori.jpg">

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


# How to use

- See examples

# License
- MIT
