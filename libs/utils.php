<?php

function nearestColor(array $palette, float $r, float $g, float $b): array
{
    $best = $palette[0];
    $bestDist = PHP_FLOAT_MAX;

    foreach ($palette as $color) {
        [$pr, $pg, $pb] = $color;

        $dr = $r - $pr;
        $dg = $g - $pg;
        $db = $b - $pb;

        $dist = $dr * $dr + $dg * $dg + $db * $db;

        if ($dist < $bestDist) {
            $bestDist = $dist;
            $best = $color;
        }
    }

    return $best;
}

function clamp(float $value): int
{
    return max(0, min(255, (int) round($value)));
}

function ditherSpectra6(GdImage $img): GdImage
{
    $palette = [
        [0, 0, 0],         // noir
        [255, 255, 255],   // blanc
        [255, 0, 0],       // rouge
        [255, 255, 0],     // jaune
        [0, 0, 255],       // bleu
        [0, 255, 0],       // vert
    ];

    $w = imagesx($img);
    $h = imagesy($img);

    $pixels = [];

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            $rgb = imagecolorat($img, $x, $y);

            $pixels[$y][$x] = [
                ($rgb >> 16) & 0xFF,
                ($rgb >> 8) & 0xFF,
                $rgb & 0xFF,
            ];
        }
    }

    $out = imagecreatetruecolor($w, $h);

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            [$oldR, $oldG, $oldB] = $pixels[$y][$x];

            [$newR, $newG, $newB] = nearestColor($palette, $oldR, $oldG, $oldB);

            $color = imagecolorallocate($out, $newR, $newG, $newB);
            imagesetpixel($out, $x, $y, $color);

            $errR = $oldR - $newR;
            $errG = $oldG - $newG;
            $errB = $oldB - $newB;

            foreach (
                [
                    [$x + 1, $y,     7 / 16],
                    [$x - 1, $y + 1, 3 / 16],
                    [$x,     $y + 1, 5 / 16],
                    [$x + 1, $y + 1, 1 / 16],
                ] as [$nx, $ny, $factor]
            ) {
                if ($nx >= 0 && $nx < $w && $ny >= 0 && $ny < $h) {
                    $pixels[$ny][$nx][0] += $errR * $factor;
                    $pixels[$ny][$nx][1] += $errG * $factor;
                    $pixels[$ny][$nx][2] += $errB * $factor;

                    $pixels[$ny][$nx][0] = clamp($pixels[$ny][$nx][0]);
                    $pixels[$ny][$nx][1] = clamp($pixels[$ny][$nx][1]);
                    $pixels[$ny][$nx][2] = clamp($pixels[$ny][$nx][2]);
                }
            }
        }
    }

    return $out;
}

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
