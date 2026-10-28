<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($description ?? 'Explore TCM One courses, learning resources, and institutional partners.') ?>">
    <meta name="theme-color" content="#f8f7f4">
    <title><?= e($title ?? 'The Code Munk') ?> · TCM One</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.19') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.19') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">
    <style>body{min-height:100vh;display:flex;flex-direction:column}.tcm-site-main{flex:1;width:100%}</style>
</head>
<body>
<?php require dirname(__DIR__) . '/partials/public-header.php'; ?>
<div class="tcm-site-main">
    <?= $content ?? '' ?>
</div>
<?php require dirname(__DIR__) . '/partials/public-footer.php'; ?>
<script src="<?= base_url('/assets/tcm-editorial.js?v=2.22') ?>"></script>
</body>
</html>
