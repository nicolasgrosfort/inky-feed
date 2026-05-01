<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = $_ENV['KDRIVE_TOKEN'];
$driveId = $_ENV['KDRIVE_DRIVE_ID'];
$fileId  = $_ENV['KDRIVE_FILE_ID'];

$client = new Client();

// 1. Retrieve all image files
$response = $client->get("https://api.infomaniak.com/3/drive/{$driveId}/files/{$fileId}/files", [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type'  => 'application/json',
    ],
    'query' => [
        'type'  => ['file'],
        'with'  => 'dropbox',
        'limit' => 200,
        'depth' => 'unlimited',
    ],
]);

$data = json_decode($response->getBody(), true);

// 2. Filter only the images and store their IDs
$imageIds = array_map(
    fn($f) => $f['id'],
    array_filter(
        $data['data'],
        fn($f) => str_starts_with($f['mime_type'] ?? '', 'image/')
    )
);

$imageIds = array_values($imageIds);

// 3. Choose a random ID and send the image
if (!empty($imageIds)) {
    $randomId = $imageIds[array_rand($imageIds)];
    $response = $client->get("https://api.infomaniak.com/2/drive/{$driveId}/files/${randomId}/download", [
        'headers' => ['Authorization' => 'Bearer ' . $token],
    ]);

    header('Content-Type: ' . $response->getHeaderLine('Content-Type'));
    echo $response->getBody();
} else {
    echo 'Aucune image trouvée.';
}
