<?php
echo str_replace('{{base}}', e(rtrim(base_url('/'), '/')), file_get_contents(__DIR__ . '/public-header.html'));
