<?php

declare(strict_types=1);

/**
 * Generate small WebP candidates for the fixed-size public hero and logo.
 * Run after replacing either source image: php scripts/generate-responsive-brand-images.php
 */
$publicPath = dirname(__DIR__).'/public';
$images = [
    [
        'source' => $publicPath.'/uploads/hero-lanyard-bekasi-custom-01.webp',
        'prefix' => 'hero-lanyard-bekasi-custom-01',
        'widths' => [192, 320, 480, 640],
    ],
    [
        'source' => $publicPath.'/uploads/hero-lanyard-bekasi-custom-02.webp',
        'prefix' => 'hero-lanyard-bekasi-custom-02',
        'widths' => [192, 320, 480, 640],
    ],
    [
        'source' => $publicPath.'/uploads/lanyard-bekasi-logo.webp',
        'prefix' => 'lanyard-bekasi-logo',
        'widths' => [160, 256],
    ],
];

foreach ($images as $definition) {
    $source = imagecreatefromwebp($definition['source']);

    if ($source === false) {
        throw new RuntimeException("Unable to read {$definition['source']}.");
    }

    $sourceWidth = imagesx($source);
    $sourceHeight = imagesy($source);

    foreach ($definition['widths'] as $width) {
        if ($width >= $sourceWidth) {
            continue;
        }

        $height = (int) round($sourceHeight * ($width / $sourceWidth));
        $target = imagecreatetruecolor($width, $height);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagefill($target, 0, 0, imagecolorallocatealpha($target, 0, 0, 0, 127));
        imagealphablending($target, true);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        $destination = $publicPath."/uploads/{$definition['prefix']}-{$width}w.webp";
        imagewebp($target, $destination, 82);
        imagedestroy($target);
        echo basename($destination)."\n";
    }

    imagedestroy($source);
}
