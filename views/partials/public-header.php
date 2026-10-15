<?php
$html = file_get_contents(__DIR__ . '/public-header.html');
$base = e(rtrim(base_url('/'), '/'));
$logo = e(base_url('/assets/logo.png'));
echo str_replace(['{{base}}', '{{logo}}'], [$base, $logo], $html);
