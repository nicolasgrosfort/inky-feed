<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../libs/utils.php';

use GuzzleHttp\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$token     = $_ENV['KDRIVE_TOKEN'];
$driveId   = $_ENV['KDRIVE_DRIVE_ID'];
$stateFile = __DIR__ . '/../storage/manual.json';

// -------------------------------------------------------------
// POST : programmer une image à afficher au prochain check du Pi
// Exemple d'appel :
// curl -X POST "https://lab.tekh.studio/inky-feed/manual/?secret=XXXX" \
//      -d "file_id=123456"
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_GET['secret'] ?? '') !== $_ENV['MANUAL_SECRET']) {
        http_response_code(403);
        exit('Forbidden');
    }

    $fileId = $_POST['file_id'] ?? null;

    if (!$fileId) {
        http_response_code(400);
        exit('file_id manquant');
    }

    file_put_contents($stateFile, json_encode([
        'file_id'  => $fileId,
        'consumed' => false,
    ]));

    echo 'OK';
    exit;
}

// -------------------------------------------------------------
// GET : appelé toutes les minutes par le Pi
// Renvoie 204 (rien à faire) tant qu'aucune image n'est programmée,
// ou l'image déjà traitée (resize/crop/dither) sinon.
// -------------------------------------------------------------
if (!file_exists($stateFile)) {
    http_response_code(204);
    exit;
}

$state = json_decode(file_get_contents($stateFile), true);

if (empty($state['file_id']) || ($state['consumed'] ?? false)) {
    http_response_code(204);
    exit;
}

$client = new Client();

$response = $client->get("https://api.infomaniak.com/2/drive/{$driveId}/files/{$state['file_id']}/download", [
    'headers' => ['Authorization' => 'Bearer ' . $token],
]);

$imageData = $response->getBody()->getContents();

$image = resizeCrop($imageData, 800, 480);

imagefilter($image, IMG_FILTER_BRIGHTNESS, 5);
imagefilter($image, IMG_FILTER_CONTRAST, -20);

$image = ditherSpectra6($image);

// Marquer comme consommée pour ne pas la ré-afficher en boucle
// chaque minute jusqu'au prochain cycle 8h/12h/16h/20h.
file_put_contents($stateFile, json_encode([
    'file_id'  => $state['file_id'],
    'consumed' => true,
]));

header('Content-Type: image/jpeg');
imagejpeg($image, null, 90);
