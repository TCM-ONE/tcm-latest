<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($desc ?? $workshop['description'] ?? '') ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($desc ?? $workshop['description'] ?? '') ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="TCM One">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= e($canonical) ?>">
    <meta name="twitter:title" content="<?= e($title) ?>">
    <meta name="twitter:description" content="<?= e($desc ?? $workshop['description'] ?? '') ?>">
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
        body { background: #fafafa; color: #111111; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        
        .ws-detail-hero {
            background: #111111;
            color: #ffffff;
            padding: 70px 0 55px;
            border-bottom: 2px solid #111111;
        }
        .ws-badge-white {
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
        .ws-detail-hero h1 {
            font-size: 2.75rem;
            font-weight: 900;
            letter-spacing: -0.04em;
            margin-bottom: 14px;
            line-height: 1.15;
        }
        .ws-detail-hero .tagline {
            font-size: 1.15rem;
            color: rgba(255, 255, 255, 0.75);
            max-width: 780px;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .ws-stat-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #ffffff;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        .ws-hero-cta-box {
            background: #1a1a1a;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            padding: 24px;
            margin-top: 24px;
        }
        .ws-price-hero {
            font-size: 2.2rem;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.03em;
        }
        .ws-price-orig {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.45);
            text-decoration: line-through;
            margin-left: 8px;
        }

        .ws-content-section {
            padding: 60px 0;
        }
        .ws-card-box {
            background: #ffffff;
            border: 1.5px solid #111111;
            padding: 32px;
            margin-bottom: 30px;
        }
        .ws-card-box h2 {
            font-size: 1.35rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            margin-bottom: 20px;
            border-bottom: 1.5px solid #111111;
            padding-bottom: 12px;
            text-transform: uppercase;
        }
        .ws-day-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid #e5e5e5;
        }
        .ws-day-item:last-child {
            border-bottom: none;
        }
        .ws-day-num {
            background: #111111;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            padding: 4px 8px;
            white-space: nowrap;
        }
        .ws-day-title {
            font-size: 0.98rem;
            font-weight: 700;
            color: #111111;
            line-height: 1.45;
        }

        .ws-check-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .ws-check-list li {
            position: relative;
            padding-left: 28px;
            margin-bottom: 14px;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.5;
        }
        .ws-check-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            top: 0;
            font-weight: 900;
            color: #111111;
        }

        .ws-btn-join {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #25D366;
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 800;
            padding: 14px 24px;
            text-decoration: none;
            border: 1.5px solid #1ebe5d;
            cursor: pointer;
            transition: opacity 0.15s ease;
        }
        .ws-btn-join:hover {
            color: #ffffff;
            opacity: 0.92;
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
                    <a href="<?= base_url('/workshops') ?>" class="tcm-btn sm ghost" style="border:1.5px solid #111;font-weight:700;">All Workshops</a>
                    <a href="<?= base_url('/programs.html') ?>" class="tcm-btn sm ghost" style="border:1.5px solid #111;font-weight:700;">Programs</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="ws-detail-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="ws-badge-white"><?= e($workshop['category']) ?> &middot; <?= e($workshop['badge']) ?></span>
                    <h1><?= e($workshop['title']) ?></h1>
                    <p class="tagline"><?= e($workshop['full_description']) ?></p>

                    <div class="d-flex flex-wrap">
                        <span class="ws-stat-chip"><i class="bi bi-clock"></i> <?= e($workshop['duration']) ?> Intensive</span>
                        <span class="ws-stat-chip"><i class="bi bi-alarm"></i> <?= e($workshop['schedule']) ?></span>
                        <span class="ws-stat-chip"><i class="bi bi-calendar-event"></i> <?= e($workshop['starts_at']) ?></span>
                        <span class="ws-stat-chip"><i class="bi bi-broadcast"></i> 100% Live Mentored</span>
                    </div>

                    <!-- Hero CTA & Share Row -->
                    <div class="ws-hero-cta-box d-flex flex-wrap align-items-center justify-content-between gap-16">
                        <div>
                            <span class="ws-price-hero">&#8377;<?= number_format((float)$workshop['price'], 0) ?></span>
                            <span class="ws-price-orig">&#8377;<?= number_format((float)$workshop['original_price'], 0) ?></span>
                            <div style="color:rgba(255,255,255,0.6);font-size:0.8rem;margin-top:2px;">One-time fee &middot; All 7 live sessions included</div>
                        </div>

                        <div class="d-flex align-items-center gap-10">
                            <?php
                                $waMsg = "Hi TCM One team! I want to join the {$workshop['short_title']} (₹{$workshop['price']}). Please guide me with enrollment.";
                                $waUrl = "https://wa.me/917987823907?text=" . rawurlencode($waMsg);
                            ?>
                            <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" class="ws-btn-join">
                                <i class="bi bi-whatsapp"></i> Register on WhatsApp
                            </a>

                            <!-- Minimalist Share Button -->
                            <button type="button"
                                    class="tcm-share-btn"
                                    data-share-url="<?= e($canonical) ?>"
                                    data-share-title="<?= e($workshop['title']) ?>"
                                    data-share-text="<?= e($workshop['description']) ?>"
                                    title="Share Workshop"
                                    aria-label="Share Workshop">
                                <i class="bi bi-share"></i>
                                <span>Share</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div style="background:#0a0a0a;border:1.5px solid rgba(255,255,255,0.25);padding:28px;">
                        <h3 style="color:#ffffff;font-size:1.15rem;font-weight:900;letter-spacing:-0.02em;margin-bottom:18px;text-transform:uppercase;">
                            Workshop Highlights
                        </h3>
                        <ul class="ws-check-list text-white-50" style="color:rgba(255,255,255,0.8) !important;">
                            <?php foreach ($workshop['highlights'] as $highlight): ?>
                                <li style="color:rgba(255,255,255,0.85);"><span style="color:#fff;"><?= e($highlight) ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content: Curriculum & Details -->
    <main class="ws-content-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Day by Day Curriculum -->
                    <div class="ws-card-box">
                        <h2>7-Day Live Curriculum Breakdown</h2>
                        <div class="ws-curriculum-list">
                            <?php foreach ($workshop['curriculum'] as $day): ?>
                            <div class="ws-day-item">
                                <span class="ws-day-num"><?= e($day['day']) ?></span>
                                <div class="ws-day-title"><?= e($day['title']) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Who is this for? -->
                    <div class="ws-card-box">
                        <h2>Who Is This Workshop For?</h2>
                        <ul class="ws-check-list">
                            <li><strong>College Students:</strong> Looking to build real-world project portfolios before campus recruitment drives.</li>
                            <li><strong>Beginners & Non-IT Career Changers:</strong> Zero coding experience needed; we start from basic mental models.</li>
                            <li><strong>Self-Taught Learners:</strong> Need structured live accountability, debugging help, and direct mentor access.</li>
                        </ul>
                    </div>
                </div>

                <!-- Sticky Sidebar -->
                <div class="col-lg-4">
                    <div class="ws-card-box" style="position:sticky;top:90px;">
                        <h2>Enrollment Summary</h2>
                        <p style="font-size:0.9rem;color:#555;line-height:1.5;margin-bottom:20px;">
                            Seats are strictly limited to keep cohort discussions interactive and ensure individual doubt clearance.
                        </p>

                        <div style="background:#fafafa;border:1px solid #e0e0e0;padding:16px;margin-bottom:20px;">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:0.85rem;color:#666;">Fee</span>
                                <strong style="font-size:1.1rem;color:#111;">&#8377;<?= number_format((float)$workshop['price'], 0) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:0.85rem;color:#666;">Duration</span>
                                <strong><?= e($workshop['duration']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span style="font-size:0.85rem;color:#666;">Access</span>
                                <strong>Lifetime Recordings</strong>
                            </div>
                        </div>

                        <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" class="ws-btn-join w-100 mb-2">
                            <i class="bi bi-whatsapp"></i> Enroll via WhatsApp
                        </a>

                        <button type="button"
                                class="tcm-share-btn w-100 justify-content-center"
                                data-share-url="<?= e($canonical) ?>"
                                data-share-title="<?= e($workshop['title']) ?>"
                                data-share-text="<?= e($workshop['description']) ?>">
                            <i class="bi bi-share"></i> Share Workshop
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer style="background:#111111;color:#ffffff;padding:40px 0;border-top:2px solid #111111;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-12">
            <div>
                <strong>TCM One</strong> &middot; The Code Munk &copy; <?= date('Y') ?>. All rights reserved.
            </div>
            <div class="d-flex gap-16">
                <a href="<?= base_url('/workshops') ?>" style="color:rgba(255,255,255,0.7);text-decoration:none;">All Workshops</a>
                <a href="<?= base_url('/programs.html') ?>" style="color:rgba(255,255,255,0.7);text-decoration:none;">Programs</a>
                <a href="<?= base_url('/terms.html') ?>" style="color:rgba(255,255,255,0.7);text-decoration:none;">Terms</a>
                <a href="<?= base_url('/privacy.html') ?>" style="color:rgba(255,255,255,0.7);text-decoration:none;">Privacy</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= base_url('/assets/tcm-share.js') ?>"></script>
</body>
</html>
