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
    <meta name="description" content="<?= e($desc ?? $course['description'] ?? '') ?>">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large">
    <link rel="canonical" href="<?= e($canonical) ?>">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($desc ?? $course['description'] ?? '') ?>">
    <meta property="og:image" content="<?= e($thumbUrl) ?>">
    <meta property="og:image:alt" content="<?= e($course['title']) ?> course by TCM One">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="TCM One">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= e($canonical) ?>">
    <meta name="twitter:title" content="<?= e($title) ?>">
    <meta name="twitter:description" content="<?= e($desc ?? $course['description'] ?? '') ?>">
    <meta name="twitter:image" content="<?= e($thumbUrl) ?>">

    <?php
        $courseSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $course['title'],
            'description' => $desc ?? ($course['description'] ?? ''),
            'url' => $canonical,
            'image' => $thumbUrl,
            'inLanguage' => 'en-IN',
            'educationalLevel' => $course['level'] ?? 'Beginner',
            'teaches' => $course['category_name'] ?? $course['title'],
            'courseMode' => 'online',
            'provider' => [
                '@type' => 'Organization',
                'name' => 'TCM One',
                'url' => $appUrl,
                'logo' => $appUrl . '/assets/logo.png',
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) ((float) ($course['price'] ?? 0)),
                'priceCurrency' => 'INR',
                'availability' => 'https://schema.org/InStock',
                'url' => $canonical,
            ],
        ];
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => $appUrl . '/',
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Courses',
                    'item' => $appUrl . '/programs.html',
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $course['title'],
                    'item' => $canonical,
                ],
            ],
        ];
    ?>
    <script type="application/ld+json"><?= json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script type="application/ld+json"><?= json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script>
      gtag('event', 'course_view', {
        course_slug: <?= json_encode((string) $course['slug']) ?>,
        course_title: <?= json_encode((string) $course['title']) ?>,
        value: <?= json_encode((float) ($course['price'] ?? 0)) ?>,
        currency: 'INR'
      });
    </script>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/landing-polish.css') ?>">
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
            align-items: center;
            column-gap: 24px;
            row-gap: 18px;
        }
        .ws-hero-price-block {
            flex: 1 1 230px;
            min-width: 0;
        }
        .ws-hero-actions {
            display: flex;
            align-items: stretch;
            justify-content: flex-end;
            flex: 1 1 420px;
            gap: 10px;
            min-width: 0;
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
            padding: 48px 0;
        }
        .ws-card-box {
            background: #ffffff;
            border: 1.5px solid #111111;
            padding: 24px 20px; /* Reduced left-right padding */
            margin-bottom: 24px;
            box-shadow: 4px 4px 0px #111111;
        }
        .ws-card-box h2 {
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
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
            gap: 7px;
            background: #25D366;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            padding: 9px 18px;
            text-decoration: none;
            border: 1.5px solid #1ebe5d;
            border-radius: 0px !important;
            cursor: pointer;
            transition: opacity 0.15s ease;
            letter-spacing: -0.01em;
            min-height: 52px;
            text-align: center;
            white-space: nowrap;
        }
        .ws-btn-join:hover {
            color: #ffffff;
            opacity: 0.92;
        }
        .ws-btn-brochure,
        .ws-hero-actions .tcm-share-btn {
            min-height: 52px;
            text-align: center;
            white-space: nowrap;
        }
        .ws-btn-brochure {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #ffffff;
            color: #111111;
            font-size: 0.95rem;
            font-weight: 700;
            padding: 14px 20px;
            text-decoration: none;
            border: 1.5px solid #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .ws-hero-actions .ws-btn-join,
        .ws-hero-actions .ws-btn-brochure {
            flex: 1 1 190px;
        }
        .ws-hero-actions .tcm-share-btn {
            flex: 0 0 auto;
            padding-inline: 18px;
        }
        @media (max-width: 1199px) {
            .ws-hero-actions {
                flex: 1 1 100%;
                justify-content: flex-start;
            }
        }
        @media (max-width: 575px) {
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
            .ws-hero-actions .ws-btn-brochure,
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
                            <a class="nav-link" href="<?= base_url('/workshops') ?>">Workshops</a>
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
                        <button type="button" class="nav-link d-none d-sm-inline-flex align-items-center gap-1 btn-trigger-brochure" data-course-slug="<?= e($course['slug']) ?>" data-course-title="<?= e($course['title']) ?>" style="background:none;border:none;font-weight:600;font-size:14px;color:#111;cursor:pointer;padding:0;">
                            <i class="bi bi-file-earmark-pdf"></i> Course Brochure PDF
                        </button>
                        <a href="<?= base_url('/login.php') ?>" class="nav-login-link d-none d-md-inline-block" style="font-size:14px;color:#111;text-decoration:none;font-weight:600;">Log in</a>
                        <a href="<?= base_url('/login.php') ?>" class="join-btn">Start Free Trial</a>
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
                    <span class="ws-badge-white"><?= e($course['category_name'] ?? 'Technology Course') ?> &middot; <?= e($course['level'] ?? 'Beginner to Advanced') ?></span>
                    <h1><?= e($course['title']) ?></h1>
                    <p class="tagline"><?= e($course['subtitle'] ?: ($course['description'] ?? 'Comprehensive career-ready engineering course from TCM One.')) ?></p>

                    <div class="d-flex flex-wrap">
                        <span class="ws-stat-chip"><i class="bi bi-clock"></i> <?= e($course['duration'] ?: 'Comprehensive') ?></span>
                        <span class="ws-stat-chip"><i class="bi bi-person-video3"></i> Mentored Cohort</span>
                        <span class="ws-stat-chip"><i class="bi bi-patch-check"></i> Certificate Included</span>
                        <span class="ws-stat-chip"><i class="bi bi-broadcast"></i> Live & Self-Paced</span>
                    </div>

                    <!-- Hero CTA & Share Row -->
                    <div class="ws-hero-cta-box d-flex flex-wrap align-items-center justify-content-between gap-16">
                        <div class="ws-hero-price-block">
                            <span class="ws-price-hero">&#8377;<?= number_format((float)($course['price'] ?? 0), 0) ?></span>
                            <?php if (!empty($course['original_price']) && (float)$course['original_price'] > (float)$course['price']): ?>
                                <span class="ws-price-orig">&#8377;<?= number_format((float)$course['original_price'], 0) ?></span>
                            <?php endif; ?>
                            <div style="color:rgba(255,255,255,0.6);font-size:0.8rem;margin-top:2px;">Full curriculum access &middot; Projects & Lifetime resources</div>
                        </div>

                        <div class="ws-hero-actions">
                            <?php
                                $waCourseMsg = "Hi TCM One! I'm interested in enrolling in the {$course['title']} course. Please guide me.";
                                $waCourseUrl = "https://wa.me/917987823907?text=" . rawurlencode($waCourseMsg);
                            ?>
                            <a href="<?= $waCourseUrl ?>" target="_blank" rel="noopener noreferrer" class="ws-btn-join">
                                <i class="bi bi-whatsapp"></i> Enroll via WhatsApp
                            </a>

                            <a href="<?= base_url('/courses/' . rawurlencode($course['slug']) . '/brochure') ?>" class="ws-btn-brochure" data-course-slug="<?= e($course['slug']) ?>" data-course-title="<?= e($course['title']) ?>">
                                <i class="bi bi-file-earmark-pdf"></i> Download Course Brochure PDF
                            </a>

                            <!-- Minimalist Share Button -->
                            <button type="button"
                                    class="tcm-share-btn"
                                    data-share-url="<?= e($canonical) ?>"
                                    data-share-title="<?= e($course['title']) ?>"
                                    data-share-text="<?= e($course['subtitle'] ?: $course['description']) ?>"
                                    title="Share Course"
                                    aria-label="Share Course">
                                <i class="bi bi-share"></i>
                                <span>Share</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div style="background:#0a0a0a;border:1.5px solid rgba(255,255,255,0.25);padding:14px;text-align:center;">
                        <?php if (!empty($thumbUrl)): ?>
                            <img src="<?= e($thumbUrl) ?>" alt="<?= e($course['title']) ?>" style="width:100%;height:auto;max-height:260px;object-fit:cover;border:1px solid rgba(255,255,255,0.15);margin-bottom:14px;display:block;">
                        <?php endif; ?>
                        <div style="color:#ffffff;font-size:0.9rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;text-align:left;padding:0 6px;">
                            Career Track &middot; <?= e($course['level'] ?? 'All Levels') ?>
                        </div>
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
                    <!-- Course Modules Curriculum -->
                    <div class="ws-card-box">
                        <h2>Curriculum & Modules</h2>
                        <?php if (!empty($curriculum)): ?>
                            <div class="ws-curriculum-list">
                                <?php foreach ($curriculum as $module): ?>
                                    <div style="margin-bottom:24px;border-bottom:1px solid #e5e5e5;padding-bottom:16px;">
                                        <h4 style="font-size:1.1rem;font-weight:800;color:#111;margin-bottom:8px;">
                                            <?= e($module['title'] ?? 'Module') ?>
                                        </h4>
                                        <?php if (!empty($module['description'])): ?>
                                            <p style="font-size:0.88rem;color:#555;margin-bottom:10px;"><?= e($module['description']) ?></p>
                                        <?php endif; ?>

                                        <?php if (!empty($module['lessons']) && is_array($module['lessons'])): ?>
                                            <div class="d-flex flex-column gap-6">
                                                <?php foreach ($module['lessons'] as $idx => $lesson): ?>
                                                    <div class="d-flex align-items-center gap-8" style="font-size:0.86rem;color:#333;">
                                                        <i class="bi bi-play-circle" style="color:#111;"></i>
                                                        <span><?= e($lesson['title'] ?? "Lesson " . ($idx + 1)) ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color:#666;font-size:0.92rem;line-height:1.6;">
                                <?= nl2br(e($course['description'] ?? 'Full curriculum includes hands-on industry projects, code reviews, and direct mentor Q&A sessions.')) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- What you will gain -->
                    <div class="ws-card-box">
                        <h2>What You Will Gain</h2>
                        <ul class="ws-check-list">
                            <li><strong>Hands-on Portfolio:</strong> Build and ship real-world production applications.</li>
                            <li><strong>Industry Mentorship:</strong> Direct doubt resolution with senior software developers.</li>
                            <li><strong>TCM One Credential:</strong> Verified Certificate of Completion shareable on LinkedIn.</li>
                        </ul>
                    </div>
                </div>

                <!-- Sticky Sidebar -->
                <div class="col-lg-4">
                    <div class="ws-card-box" style="position:sticky;top:90px;">
                        <h2>Enrollment Summary</h2>
                        <div style="background:#fafafa;border:1px solid #e0e0e0;padding:16px;margin-bottom:20px;">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:0.85rem;color:#666;">Course Fee</span>
                                <strong style="font-size:1.1rem;color:#111;">&#8377;<?= number_format((float)($course['price'] ?? 0), 0) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:0.85rem;color:#666;">Access</span>
                                <strong>Lifetime Access</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span style="font-size:0.85rem;color:#666;">Format</span>
                                <strong>Self-Paced + Live Mentorship</strong>
                            </div>
                        </div>

                        <a href="<?= $waCourseUrl ?>" target="_blank" rel="noopener noreferrer" class="ws-btn-join w-100 mb-2">
                            <i class="bi bi-whatsapp"></i> Enroll via WhatsApp
                        </a>

                        <button type="button"
                                class="tcm-share-btn w-100 justify-content-center"
                                data-share-url="<?= e($canonical) ?>"
                                data-share-title="<?= e($course['title']) ?>"
                                data-share-text="<?= e($course['subtitle'] ?: $course['description']) ?>">
                            <i class="bi bi-share"></i> Share Course
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
    <script src="<?= base_url('/assets/tcm-app.js') ?>"></script>
</body>
</html>
