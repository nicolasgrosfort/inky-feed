<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

// Functions

function fixExifOrientation(GdImage $image, string $imageData): GdImage
{
    if (!function_exists('exif_read_data')) {
        return $image;
    }

    $tmp = tempnam(sys_get_temp_dir(), 'img_');
    file_put_contents($tmp, $imageData);

    $exif = @exif_read_data($tmp);
    unlink($tmp);

    $orientation = $exif['Orientation'] ?? 1;

    return match ($orientation) {
        3 => imagerotate($image, 180, 0),
        6 => imagerotate($image, -90, 0), // 90° horaire
        8 => imagerotate($image, 90, 0),  // 90° antihoraire
        default => $image,
    };
}

function resizeCrop(string $imageData, int $targetW, int $targetH): GdImage
{
    $src = imagecreatefromstring($imageData);

    if (!$src) {
        throw new RuntimeException("Impossible de charger l'image.");
    }

    $src = fixExifOrientation($src, $imageData);
    $src = imagerotate($src, 90, 0);

    $srcW = imagesx($src);
    $srcH = imagesy($src);

    $srcRatio = $srcW / $srcH;
    $targetRatio = $targetW / $targetH;

    if ($srcRatio > $targetRatio) {
        $cropH = $srcH;
        $cropW = (int) round($cropH * $targetRatio);
        $cropX = (int) round(($srcW - $cropW) / 2);
        $cropY = 0;
    } else {
        $cropW = $srcW;
        $cropH = (int) round($cropW / $targetRatio);
        $cropX = 0;
        $cropY = (int) round(($srcH - $cropH) / 2);
    }

    $dst = imagecreatetruecolor($targetW, $targetH);

    imagecopyresampled(
        $dst,
        $src,
        0,
        0,
        $cropX,
        $cropY,
        $targetW,
        $targetH,
        $cropW,
        $cropH
    );

    return $dst;
}

// Project

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

    $response = $client->get("https://api.infomaniak.com/2/drive/{$driveId}/files/{$randomId}/download", [
        'headers' => ['Authorization' => 'Bearer ' . $token],
    ]);

    $imageData = $response->getBody()->getContents();

    $image = resizeCrop($imageData, 800, 480);
    imagefilter($image, IMG_FILTER_BRIGHTNESS, 5);
    imagefilter($image, IMG_FILTER_CONTRAST, -15);

    $matrix = [
        [-1, -1, -1],
        [-1, 16, -1],
        [-1, -1, -1],
    ];

    imageconvolution($image, $matrix, 8, 0);

    header('Content-Type: image/jpeg');
    imagejpeg($image, null, 90);
} else {
    echo 'Aucune image trouvée.';
}
