# What is libnfc-for-php?

This library is a toy for me. You can read NFC with libnfc written in PHP.

# Requirements
- PHP 7.4 or later
- PHP FFI
- libnfc

# Tested

- Mac Big Sur
- FeliCa
- PaSoRi RC-S330
  - https://www.amazon.co.jp/dp/B001MVPD8U/

# Warning

- PaSoRi RC-S380 is not supported the libnfc. I have a roadmap to implement RC-S380.
  - It is required to develop a driver. For example, the nfcpy has been implemented.
    - see: https://github.com/nfcpy/nfcpy/blob/master/src/nfc/clf/rcs380.py

<img src="images/pasori.jpg">

# Quick start

## Mac
1. Install libnfc

```
$ brew install libnfc
```

2. Install this library

```
$ composer require m3m0r7/libnfc-for-php
```

3. Connect NFC Device into your machine.
4. Run example code

```
$ php examples/nfc-poll-simple.php
```

5. Put your NFC

<img src="images/felica.png">

6. You can get output.

<img src="images/example.jpg">

# License
- MIT
