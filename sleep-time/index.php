<?php
// Charger le .env
$envPath = __DIR__ . '/../.env';
$sleepTime = 300; // valeur par défaut

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), 'SLEEP_TIME=') === 0) {
            $sleepTime = trim(substr($line, strlen('SLEEP_TIME=')));
            break;
        }
    }
}

header('Content-Type: text/plain');
echo $sleepTime;
