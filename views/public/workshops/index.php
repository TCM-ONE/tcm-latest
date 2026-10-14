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
    <meta name="description" content="<?= e($description) ?>">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large">
    <link rel="canonical" href="<?= e($canonical) ?>">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:image:alt" content="TCM One live coding workshops">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="TCM One">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= e($canonical) ?>">
    <meta name="twitter:title" content="<?= e($title) ?>">
    <meta name="twitter:description" content="<?= e($description) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <?php
        $itemList = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => [],
        ];
        $position = 1;
        foreach ($workshops as $w) {
            $itemList['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'url' => $appUrl . '/workshops/' . rawurlencode($w['slug']),
                'name' => $w['title'],
            ];
        }
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $appUrl . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Workshops', 'item' => $canonical],
            ],
        ];
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => 'TCM One',
            'url' => $appUrl,
            'logo' => $appUrl . '/assets/logo.png',
            'areaServed' => 'IN',
        ];
    ?>
    <script type="application/ld+json"><?= json_encode($itemList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script type="application/ld+json"><?= json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script type="application/ld+json"><?= json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

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
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 18px;
            padding: 42px 0 64px;
        }
        .ws-card {
            background: #ffffff;
            border: 1.5px solid #111111;
            box-shadow: 4px 4px 0px #111111;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .ws-card:hover {
            transform: translate(-1px, -1px);
            box-shadow: 5px 5px 0px #111111;
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
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 10px;
            color: #111111;
        }
        .ws-card p {
            font-size: 0.82rem;
            color: #555555;
            line-height: 1.55;
            margin-bottom: 14px;
            flex: 1;
        }
        .ws-meta-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 0.74rem;
            font-weight: 600;
            color: #222222;
            padding: 10px 0;
            border-top: 1px solid #eeeeee;
            border-bottom: 1px solid #eeeeee;
            margin-bottom: 14px;
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
            font-size: 1.45rem;
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
            min-height: 38px;
            background: #111111;
            color: #ffffff;
            font-size: 0.8rem;
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
                        <a href="<?= base_url('/login.php') ?>" class="join-btn">Start Free Trial</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="ws-hero">
        <div class="container">
            <span class="ws-badge">Live Cohorts &middot; Beginner Friendly</span>
            <h1>TCM One Workshops</h1>
            <p>Practical, mentor-led workshops for college students and beginners. Build useful skills through focused live sessions, guided exercises and clear next steps.</p>
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
</body>
</html>
