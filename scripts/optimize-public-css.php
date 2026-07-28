<?php

declare(strict_types=1);

$path = dirname(__DIR__).DIRECTORY_SEPARATOR.'public/css/app.min.css';
$css = file_get_contents($path) ?: throw new RuntimeException("Cannot read {$path}");

foreach ([500, 600, 800] as $weight) {
    $css = preg_replace(
        '/@font-face\s*\{(?=[^{}]*font-family:\s*[\'\"]?Geist)(?=[^{}]*font-weight:\s*'.$weight.'\b)[^{}]*\}/i',
        '',
        $css,
    );
}

$css = preg_replace(
    '#/fonts/material-symbols/MaterialSymbolsOutlined\.woff2(?:\?v=\d+)?#',
    '/fonts/material-symbols/MaterialSymbolsOutlined.woff2?v=2',
    $css,
);

$css = preg_replace(
    '/(@font-face\s*\{(?=[^{}]*font-family:\s*Material Symbols Outlined)[^{}]*font-display:)\s*block\b/i',
    '$1swap',
    $css,
);

if (file_put_contents($path, $css) === false) {
    throw new RuntimeException("Cannot write {$path}");
}
