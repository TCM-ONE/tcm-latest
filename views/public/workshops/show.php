<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-19F749QEBF"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-19F749QEBF');
    </script>
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($desc ?? $workshop['description'] ?? '') ?>">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large">
    <link rel="canonical" href="<?= e($canonical) ?>">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($desc ?? $workshop['description'] ?? '') ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:image:alt" content="<?= e($workshop['title']) ?> live workshop by TCM One">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="TCM One">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= e($canonical) ?>">
    <meta name="twitter:title" content="<?= e($title) ?>">
    <meta name="twitter:description" content="<?= e($desc ?? $workshop['description'] ?? '') ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <?php
        $webPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $workshop['title'],
            'description' => $desc ?? ($workshop['description'] ?? ''),
            'url' => $canonical,
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'TCM One',
                'url' => $appUrl,
                'logo' => $appUrl . '/assets/logo.png',
            ],
        ];
        $eventSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationEvent',
            'name' => $workshop['title'],
            'description' => $desc ?? ($workshop['description'] ?? ''),
            'url' => $canonical,
            'image' => $ogImage,
            'eventAttendanceMode' => 'https://schema.org/OnlineEventAttendanceMode',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'organizer' => [
                '@type' => 'EducationalOrganization',
                'name' => 'TCM One',
                'url' => $appUrl,
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) ((float) ($workshop['price'] ?? 0)),
                'priceCurrency' => 'INR',
                'availability' => 'https://schema.org/InStock',
                'url' => $canonical,
            ],
        ];
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $appUrl . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Workshops', 'item' => $appUrl . '/workshops'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $workshop['title'], 'item' => $canonical],
            ],
        ];
    ?>
    <script type="application/ld+json"><?= json_encode($webPageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script type="application/ld+json"><?= json_encode($eventSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script type="application/ld+json"><?= json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script>
      gtag('event', 'workshop_view', {
        workshop_slug: <?= json_encode((string) $workshop['slug']) ?>,
        workshop_title: <?= json_encode((string) $workshop['title']) ?>,
        value: <?= json_encode((float) ($workshop['price'] ?? 0)) ?>,
        currency: 'INR'
      });
    </script>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/site-polish.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/landing-polish.css') ?>">
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
            background: #181818;
            border: 1.5px solid rgba(255, 255, 255, 0.22);
            padding: 22px 24px;
            margin-top: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .ws-hero-price-block {
            width: 100%;
        }
        .ws-price-hero-wrap {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 6px;
        }
        .ws-price-hero {
            font-size: 2.3rem;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.03em;
            line-height: 1;
        }
        .ws-price-orig {
            font-size: 1.15rem;
            color: rgba(255, 255, 255, 0.45);
            text-decoration: line-through;
            font-weight: 600;
        }
        .ws-price-discount-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(37, 211, 102, 0.15);
            color: #25D366;
            border: 1px solid rgba(37, 211, 102, 0.4);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 3px 8px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .ws-hero-price-subtext {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.84rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .ws-hero-price-subtext i {
            color: #25D366;
            font-size: 0.95rem;
        }
        .ws-hero-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.12);
            width: 100%;
            margin: 0;
        }
        .ws-hero-actions {
            display: flex;
            align-items: stretch;
            gap: 10px;
            width: 100%;
            flex-wrap: wrap;
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
            font-size: 0.92rem;
            font-weight: 700;
            padding: 12px 20px;
            text-decoration: none;
            border: 1.5px solid #1ebe5d;
            border-radius: 0px !important;
            cursor: pointer;
            transition: all 0.15s ease;
            letter-spacing: -0.01em;
            min-height: 48px;
            text-align: center;
            white-space: nowrap;
            flex: 1 1 220px;
        }
        .ws-btn-join:hover {
            background: #20ba59;
            border-color: #20ba59;
            color: #ffffff;
        }
        .ws-hero-actions .tcm-share-btn {
            min-height: 48px;
            text-align: center;
            white-space: nowrap;
            flex: 0 0 auto;
            padding-inline: 18px;
            font-size: 0.92rem;
            border: 1.5px solid #ffffff;
        }
        @media (max-width: 640px) {
            .ws-detail-hero {
                padding: 48px 0 38px;
            }
            .ws-detail-hero h1 {
                font-size: 2rem;
            }
            .ws-hero-cta-box {
                padding: 18px;
            }
            .ws-hero-actions {
                flex-direction: column;
            }
            .ws-hero-actions .ws-btn-join,
            .ws-hero-actions .tcm-share-btn {
                width: 100%;
                flex-basis: auto;
            }
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
                <button class="navbar-toggler border-0 shadow-none d-lg-none ms-auto" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav me-auto align-items-lg-center">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('/workshops') ?>" style="font-weight:700;color:#000;">Workshops</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/insights.html') ?>">TCM Insights</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/community.html') ?>">Community</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/contact.html') ?>">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/partner.html') ?>">Become a Partner</a>
                        </li>
                    </ul>

                    <div class="header-right-actions d-flex align-items-center gap-3">
                        <a href="<?= base_url('/download-brochure') ?>" class="nav-link d-none d-sm-inline-flex align-items-center gap-1" download="TCM-One-Course-Brochure.pdf" style="font-weight:600;font-size:14px;color:#111;">
                            <i class="bi bi-file-earmark-pdf"></i> Course Brochure PDF
                        </a>
                        <a href="<?= base_url('/login.php') ?>" class="nav-login-link d-none d-md-inline-block" style="font-size:14px;color:#111;text-decoration:none;font-weight:600;">Log in</a>
                        <button type="button" class="join-btn demo-trigger">Get a Free Demo</button>
                    </div>
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
                    <div class="ws-hero-cta-box">
                        <div class="ws-hero-price-block">
                            <div class="ws-price-hero-wrap">
                                <span class="ws-price-hero">&#8377;<?= number_format((float)$workshop['price'], 0) ?></span>
                                <?php if (!empty($workshop['original_price']) && (float)$workshop['original_price'] > (float)$workshop['price']): 
                                    $origWp = (float)$workshop['original_price'];
                                    $currWp = (float)$workshop['price'];
                                    $discountWp = round((($origWp - $currWp) / $origWp) * 100);
                                ?>
                                    <span class="ws-price-orig">&#8377;<?= number_format($origWp, 0) ?></span>
                                    <span class="ws-price-discount-badge"><?= $discountWp ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                            <div class="ws-hero-price-subtext">
                                <i class="bi bi-patch-check-fill"></i>
                                <span>One-time fee &middot; 100% Live sessions &middot; Certificate included</span>
                            </div>
                        </div>

                        <div class="ws-hero-divider"></div>

                        <div class="ws-hero-actions">
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
                        <h2><?= e($workshop['duration']) ?> Live Curriculum Breakdown</h2>
                        <div class="ws-curriculum-list">
                            <?php foreach ($workshop['curriculum'] as $day): ?>
                            <div class="ws-day-item">
                                <span class="ws-day-num"><?= e($day['day']) ?></span>
                                <div class="ws-day-content" style="flex:1;">
                                    <div class="ws-day-title"><?= e($day['title']) ?></div>
                                    <?php if (!empty($day['topics'])): ?>
                                    <ul style="list-style:none;padding-left:0;margin:10px 0 0;display:flex;flex-direction:column;gap:6px;">
                                        <?php foreach ($day['topics'] as $topic): ?>
                                        <li style="font-size:0.88rem;color:#444;line-height:1.5;position:relative;padding-left:18px;">
                                            <span style="position:absolute;left:0;top:1px;color:#111;font-weight:700;">&bull;</span>
                                            <?= e($topic) ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Who is this for? -->
                    <div class="ws-card-box">
                        <h2>Who Is This Workshop For?</h2>
                        <ul class="ws-check-list">
                            <?php if (!empty($workshop['target_audience'])): ?>
                                <?php foreach ($workshop['target_audience'] as $audienceItem): ?>
                                    <?php
                                        if (is_array($audienceItem)) {
                                            $audTitle = $audienceItem['title'] ?? '';
                                            $audDesc = $audienceItem['desc'] ?? '';
                                        } elseif (str_contains((string)$audienceItem, ':')) {
                                            [$audTitle, $audDesc] = explode(':', (string)$audienceItem, 2);
                                        } else {
                                            $audTitle = '';
                                            $audDesc = (string)$audienceItem;
                                        }
                                    ?>
                                    <li>
                                        <?php if ($audTitle !== ''): ?><strong><?= e(trim($audTitle)) ?>:</strong> <?php endif; ?>
                                        <?= e(trim($audDesc)) ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><strong>College Students:</strong> Looking to build real-world project portfolios before campus recruitment drives.</li>
                                <li><strong>Beginners & Non-IT Career Changers:</strong> Zero coding experience needed; we start from basic mental models.</li>
                                <li><strong>Self-Taught Learners:</strong> Need structured live accountability, debugging help, and direct mentor access.</li>
                            <?php endif; ?>
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

    <!-- ========== FOOTER ========== -->
    <footer class="footer">
        <div class="container">
            <div class="footer-card">
                <div class="footer-top">

                    <!-- Brand Column -->
                    <div class="footer-brand">
                        <a href="<?= base_url('/') ?>" class="footer-logo">TCM <span class="logo-one">One</span></a>
                        <p>
                            Learn industry-ready skills, build real-world projects and launch your tech career with The Code Munk.
                        </p>
                        <div class="footer-social">
                            <a href="https://www.instagram.com/tcmoneindia?igsi=ZDdmM2xxZjNwNXIw&amp;utm_source=qr" target="_blank" rel="noopener" title="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="https://youtube.com/@tcmoneindia?si=8K2zGA0ki3LWJt7g" target="_blank" rel="noopener" title="YouTube"><i class="bi bi-youtube"></i></a>
                            <a href="https://discord.gg/ERrrxgePj" target="_blank" rel="noopener" title="Discord"><i class="bi bi-discord"></i></a>
                            <a href="https://tcmone.slack.com/archives/D0BUKS3K38F" target="_blank" rel="noopener" title="Slack"><i class="bi bi-slack"></i></a>
                            <a href="https://github.com/tcmone" target="_blank" rel="noopener" title="GitHub"><i class="bi bi-github"></i></a>
                            <a href="https://dev.to/tcmone" target="_blank" rel="noopener" title="Dev.to"><i class="bi bi-journal-code"></i></a>
                            <a href="https://stackoverflow.com/users/33091425/tcmone" target="_blank" rel="noopener" title="Stack Overflow"><i class="bi bi-stack-overflow"></i></a>
                            <a href="https://tcmonesspace.quora.com/?ch=17&amp;oid=8193858&amp;share=396067ef&amp;srid=5IUClU&amp;target_type=tribe" target="_blank" rel="noopener" title="Quora"><i class="bi bi-question-diamond"></i></a>
                            <a href="https://www.facebook.com/share/1e8RrFuVeD/?mibextid=wwXIfr" target="_blank" rel="noopener" title="Facebook"><i class="bi bi-facebook"></i></a>
                        </div>
                        <div class="footer-tag">
                            <i class="bi bi-patch-check-fill text-success"></i>
                            Student-Focused Learning Platform
                        </div>
                    </div>

                    <!-- Navigation Links Columns -->
                    <div class="footer-links">

                        <div class="footer-column">
                            <h4>Learn &amp; Programs</h4>
                            <a href="<?= base_url('/programs.html') ?>">Full Stack Web Dev</a>
                            <a href="<?= base_url('/programs.html') ?>">Data Science &amp; AI</a>
                            <a href="<?= base_url('/programs.html') ?>">Python &amp; Automation</a>
                            <a href="<?= base_url('/workshops') ?>">Live Workshops</a>
                        </div>

                        <div class="footer-column">
                            <h4>Ecosystem</h4>
                            <a href="<?= base_url('/programs.html') ?>">Full Stack Web Dev</a>
                            <a href="<?= base_url('/programs.html') ?>">Data Science &amp; AI</a>
                            <a href="<?= base_url('/programs.html') ?>">Cloud DevOps</a>
                            <a href="<?= base_url('/programs.html') ?>">Python &amp; Automation</a>
                        </div>

                        <div class="footer-column">
                            <h4>Legal &amp; Policy</h4>
                            <a href="<?= base_url('/privacy.html') ?>">Privacy Policy</a>
                            <a href="<?= base_url('/terms.html') ?>">Terms &amp; Conditions</a>
                            <a href="<?= base_url('/sitemap.html') ?>">Site Map</a>
                            <a href="<?= base_url('/partner.html') ?>">Partnership Policy</a>
                        </div>

                        <div class="footer-column">
                            <h4>Company &amp; Help</h4>
                            <a href="<?= base_url('/insights.html') ?>">About Us</a>
                            <a href="<?= base_url('/community.html') ?>">Community</a>
                            <a href="<?= base_url('/contact.html') ?>">Contact Us</a>
                            <a href="<?= base_url('/download-brochure') ?>" download="TCM-One-Course-Brochure.pdf"><i class="bi bi-file-earmark-pdf"></i> Course Brochure PDF</a>
                            <a href="<?= base_url('/login.php') ?>">Student Sign In</a>
                        </div>

                    </div>
                </div>

                <!-- Footer Bottom Strip -->
                <div class="footer-bottom">
                    <p>&copy; <?= date('Y') ?> The Code Munk. All rights reserved.</p>
                    <div class="footer-bottom-links">
                        <a href="<?= base_url('/privacy.html') ?>">Privacy Policy</a>
                        <a href="<?= base_url('/terms.html') ?>">Terms of Service</a>
                        <a href="<?= base_url('/sitemap.html') ?>">Sitemap</a>
                        <a href="<?= base_url('/download-brochure') ?>" download="TCM-One-Course-Brochure.pdf"><i class="bi bi-file-earmark-pdf"></i> Download Course Brochure PDF</a>
                        <a href="<?= base_url('/contact.html') ?>">Contact</a>
                        <a href="<?= base_url('/insights.html') ?>">About</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('/assets/tcm-share.js') ?>"></script>
    <script src="<?= base_url('/assets/tcm-app.js?v=2.2.4') ?>"></script>
</body>
</html>
