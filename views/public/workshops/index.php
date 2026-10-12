<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="TCM One">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= e($canonical) ?>">
    <meta name="twitter:title" content="<?= e($title) ?>">
    <meta name="twitter:description" content="<?= e($description) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/site-polish.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/tcm-share.css') ?>">

    <style>
        *, *::before, *::after { border-radius: 0 !important; }
        body { background: #fafafa; color: #111111; }
        .ws-hero {
            background: #111111;
            color: #ffffff;
            padding: 80px 0 60px;
            border-bottom: 2px solid #111111;
        }
        .ws-badge {
            display: inline-block;
            background: #ffffff;
            color: #111111;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 4px 10px;
            margin-bottom: 16px;
        }
        .ws-hero h1 {
            font-size: 2.8rem;
            font-weight: 900;
            letter-spacing: -0.04em;
            margin-bottom: 14px;
            line-height: 1.15;
        }
        .ws-hero p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.7);
            max-width: 620px;
            margin-bottom: 0;
            line-height: 1.6;
        }
        .ws-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            padding: 60px 0 80px;
        }
        .ws-card {
            background: #ffffff;
            border: 2px solid #111111;
            box-shadow: 6px 6px 0px #111111;
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .ws-card:hover {
            transform: translate(-2px, -2px);
            box-shadow: 8px 8px 0px #111111;
        }
        .ws-card-tag {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #666666;
            margin-bottom: 10px;
        }
        .ws-card h3 {
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 10px;
            color: #111111;
        }
        .ws-card p {
            font-size: 0.88rem;
            color: #555555;
            line-height: 1.55;
            margin-bottom: 20px;
            flex: 1;
        }
        .ws-meta-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 0.8rem;
            font-weight: 600;
            color: #222222;
            padding: 12px 0;
            border-top: 1px solid #eeeeee;
            border-bottom: 1px solid #eeeeee;
            margin-bottom: 20px;
        }
        .ws-meta-row span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .ws-price-row {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 16px;
        }
        .ws-price {
            font-size: 1.8rem;
            font-weight: 900;
            color: #111111;
        }
        .ws-original {
            font-size: 0.95rem;
            color: #888888;
            text-decoration: line-through;
        }
        .ws-cta-row {
            display: flex;
            gap: 8px;
            align-items: stretch;
        }
        .ws-btn-primary {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            background: #111111;
            color: #ffffff;
            font-size: 0.88rem;
            font-weight: 800;
            text-decoration: none;
            border: 2px solid #111111;
            transition: background 0.15s ease;
        }
        .ws-btn-primary:hover {
            background: #333333;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="header" style="background:#fff;border-bottom:2px solid #111;">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0">
                <a class="navbar-brand logo" href="<?= base_url('/') ?>">
                    <img src="<?= base_url('/assets/logo.png') ?>" alt="TCM One Logo" class="logo-img">
                    <span class="logo-text">TCM <span class="logo-one">One</span></span>
                </a>
                <div class="ms-auto d-flex gap-8 align-items-center">
                    <a href="<?= base_url('/programs.html') ?>" class="tcm-btn sm ghost" style="border:1.5px solid #111;font-weight:700;">Programs</a>
                    <a href="<?= base_url('/login.php') ?>" class="tcm-btn sm dark" style="background:#111;color:#fff;font-weight:700;">Join Now</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="ws-hero">
        <div class="container">
            <span class="ws-badge">Live Cohorts &middot; 7-Day Intensive</span>
            <h1>TCM One Workshops</h1>
            <p>Practical, mentor-led live coding bootcamps for college students and beginners. Built for real skills, fast progress and portfolio deployment.</p>
        </div>
    </section>

    <!-- Workshop Cards Grid -->
    <main class="container">
        <div class="ws-grid">
            <?php foreach ($workshops as $w):
                $wUrl = $appUrl . '/workshops/' . rawurlencode($w['slug']);
            ?>
            <article class="ws-card">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="ws-card-tag"><?= e($w['category']) ?></span>
                        <span style="font-size:.72rem;font-weight:800;background:#111;color:#fff;padding:2px 8px;"><?= e($w['badge']) ?></span>
                    </div>
                    <h3><?= e($w['short_title']) ?></h3>
                    <p><?= e($w['tagline']) ?></p>

                    <div class="ws-meta-row">
                        <span><i class="bi bi-clock"></i> <?= e($w['duration']) ?></span>
                        <span><i class="bi bi-calendar3"></i> <?= e($w['starts_at']) ?></span>
                        <span><i class="bi bi-broadcast"></i> Live Online</span>
                    </div>
                </div>

                <div>
                    <div class="ws-price-row">
                        <span class="ws-price">&#8377;<?= number_format((float)$w['price'], 0) ?></span>
                        <span class="ws-original">&#8377;<?= number_format((float)$w['original_price'], 0) ?></span>
                    </div>
                    <div class="ws-cta-row">
                        <a href="<?= e($wUrl) ?>" class="ws-btn-primary">
                            View Workshop &rarr;
                        </a>
                        <button type="button"
                                class="tcm-share-btn icon-only"
                                data-share-url="<?= e($wUrl) ?>"
                                data-share-title="<?= e($w['title']) ?>"
                                data-share-text="<?= e($w['description']) ?>"
                                title="Share Workshop"
                                aria-label="Share Workshop">
                            <i class="bi bi-share"></i>
                        </button>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer style="background:#111;color:#fff;padding:40px 0;border-top:2px solid #111;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-12">
            <div>
                <strong>TCM One</strong> &middot; The Code Munk &copy; <?= date('Y') ?>. All rights reserved.
            </div>
            <div class="d-flex gap-16">
                <a href="<?= base_url('/terms.html') ?>" style="color:rgba(255,255,255,0.7);text-decoration:none;">Terms</a>
                <a href="<?= base_url('/privacy.html') ?>" style="color:rgba(255,255,255,0.7);text-decoration:none;">Privacy</a>
                <a href="<?= base_url('/contact.html') ?>" style="color:rgba(255,255,255,0.7);text-decoration:none;">Contact</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= base_url('/assets/tcm-share.js') ?>"></script>
</body>
</html>
