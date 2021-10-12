<?php

require __DIR__ . '/../vendor/autoload.php';

use NFC\NFC;

$NFC = new NFC(\NFC\Drivers\RCS380\Kernel::class);
$context = $NFC->createContext();

echo "NFC Version: {$context->getVersion()}\n";
