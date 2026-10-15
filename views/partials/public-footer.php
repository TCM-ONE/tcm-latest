<?php
// Static pages are synced from this same template by npm run sync:footer.
echo str_replace('{{base}}', e(rtrim(base_url('/'), '/')), file_get_contents(__DIR__ . '/public-footer.html'));
