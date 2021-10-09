<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;

$nfc = new NFC();
$context = $nfc->createContext();

echo "NFC Version: {$context->getVersion()}\n";
