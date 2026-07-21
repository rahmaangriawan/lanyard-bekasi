<?php

declare(strict_types=1);

function resizeWebp(string $source, string $target, int $targetWidth, int $quality = 78): void
{
    [$width, $height] = getimagesize($source) ?: throw new RuntimeException("Cannot read {$source}");
    $targetHeight = (int) round($height * ($targetWidth / $width));
    $input = imagecreatefromwebp($source);
    $output = imagecreatetruecolor($targetWidth, $targetHeight);

    imagealphablending($output, false);
    imagesavealpha($output, true);
    imagecopyresampled($output, $input, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

    if (! imagewebp($output, $target, $quality)) {
        throw new RuntimeException("Cannot write {$target}");
    }

    imagedestroy($input);
    imagedestroy($output);
}

$public = dirname(__DIR__).DIRECTORY_SEPARATOR.'public';

foreach (['hero-lanyard-slider-01', 'hero-lanyard-slider-02'] as $name) {
    foreach ([320, 640] as $width) {
        resizeWebp(
            $public."/uploads/{$name}.webp",
            $public."/uploads/{$name}-{$width}w.webp",
            $width,
            76,
        );
    }
}

foreach (range(1, 5) as $index) {
    foreach ([192, 384] as $width) {
        resizeWebp(
            $public."/images/trust/trust-{$index}.webp",
            $public."/images/trust/trust-{$index}-{$width}w.webp",
            $width,
            74,
        );
    }
}
