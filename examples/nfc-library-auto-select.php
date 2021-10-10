<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;

$NFC = new NFC();
$context = $NFC->createContext();

echo "NFC Version: {$context->getVersion()}\n";
