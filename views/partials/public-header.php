<?php
$html = file_get_contents(__DIR__ . '/public-header.html');
$base = e(rtrim(base_url('/'), '/'));
$basePath = (string) config('app.base_path', '');
$logo = e(($basePath !== '' ? rtrim($basePath, '/') : '') . '/assets/logo.png');
echo str_replace(['{{base}}', '{{logo}}'], [$base, $logo], $html);

