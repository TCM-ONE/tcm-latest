<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'The Code Munk') ?> · The Code Munk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.18') ?>">

    <style>
    *, *::before, *::after {
        box-sizing: border-box;
        border-radius: 0px !important;
    }
    body {
        background: #F8F7F4;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #111111;
        margin: 0;
        padding: 0;
    }

    .auth-shell {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Top nav bar */
    .auth-nav {
        background: #FFFFFF;
        border-bottom: 1px solid #DEDDD8;
        padding: 0 clamp(16px, 4vw, 36px);
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .auth-nav-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        font-weight: 800;
        color: #111111;
        text-decoration: none;
        letter-spacing: -0.02em;
    }
    .auth-nav-brand img {
        height: 28px;
        width: auto;
    }
    .auth-nav-right {
        font-size: 13px;
        color: #666666;
    }
    .auth-nav-right a {
        color: #111111;
        font-weight: 600;
        text-decoration: none;
    }
    .auth-nav-right a:hover {
        text-decoration: underline;
    }

    /* Page body */
    .auth-body {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px 60px;
    }

    /* Card */
    .auth-card {
        width: 100%;
        max-width: 520px;
        background: #FFFFFF;
        border: 1px solid #DEDDD8;
        padding: clamp(24px, 5vw, 44px);
    }
    .auth-card h1 {
        font-family: 'Instrument Serif', Georgia, serif;
        font-size: 2.2rem;
        font-weight: 400;
        color: #111111;
        line-height: 1.15;
        margin: 0 0 8px 0;
    }
    .auth-card p.sub {
        font-size: 14px;
        color: #666666;
        margin: 0 0 24px 0;
        line-height: 1.5;
    }

    /* Form Fields */
    .tcm-field {
        margin-bottom: 18px;
    }
    .tcm-field label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #555555;
        margin-bottom: 6px;
    }
    .tcm-input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #DEDDD8;
        background: #FFFFFF;
        font-size: 14px;
        color: #111111;
        outline: none;
        transition: border-color 0.15s ease;
    }
    .tcm-input:focus {
        border-color: #111111;
    }
    .tcm-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    @media (max-width: 480px) {
        .tcm-grid-2 {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
    .tcm-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 20px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        border: 1px solid #111111;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .tcm-btn.primary {
        background: #111111;
        color: #FFFFFF;
    }
    .tcm-btn.primary:hover {
        background: #2b2b2b;
    }
    </style>
</head>
<body>
<div class="auth-shell">

    <!-- Top bar -->
    <nav class="auth-nav">
        <a href="<?= base_url('/') ?>" class="auth-nav-brand">
            <img src="<?= base_url('/assets/logo.png') ?>" alt="TCM One">
            <span>TCM<span style="font-weight: 300;">ONE</span></span>
        </a>
        <div class="auth-nav-right">
            Need help? <a href="<?= base_url('/contact') ?>">Contact Admissions</a>
        </div>
    </nav>

    <!-- Body -->
    <div class="auth-body">
        <div class="auth-card">
            <?= $content ?? '' ?>
        </div>
    </div>

</div>
</body>
</html>
