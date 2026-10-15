<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Favicon & App Icons -->
  <link rel="icon" type="image/svg+xml" href="<?= base_url('/favicon.svg') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('/favicon.png') ?>">
  <link rel="shortcut icon" href="<?= base_url('/favicon.ico') ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('/assets/logo.png') ?>">

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-19F749QEBF"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-19F749QEBF');
  </script>

  <meta name="app-base" content="<?= e(rtrim((string) config('app.url', ''), '/')) ?>">

  <title><?= e($title ?? 'Institutional & College Partnerships — TCM One') ?></title>
  <meta name="description" content="<?= e($description ?? 'Partner with TCM One to offer structured technical training, practical projects, and career-focused learning to your students.') ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical ?? 'https://thecodemunk.in/for-institutions') ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:title" content="College &amp; Training Institute Partnerships &mdash; TCM One">
  <meta property="og:description" content="<?= e($description) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Typography & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Design System Stylesheets -->
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/cai-assistant.css') ?>">

  <style>
    .inst-grid-2 {
      display: grid;
      grid-template-columns: 1fr;
      gap: 32px;
    }
    @media (min-width: 992px) {
      .inst-grid-2 {
        grid-template-columns: 1fr 1fr;
        gap: 48px;
      }
    }
    .inst-track-card {
      background: var(--tcm-surface);
      border: 1px solid var(--tcm-border);
      padding: 32px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: border-color 0.15s ease;
    }
    .inst-track-card:hover {
      border-color: var(--tcm-fg);
    }
    .inst-step-num {
      font-family: 'Instrument Serif', Georgia, serif;
      font-size: 36px;
      color: var(--tcm-muted);
      line-height: 1;
      margin-bottom: 12px;
    }
  </style>
</head>
<body>

  <!-- Announcement Bar -->
  <div class="tcm-announcement" role="region" aria-label="Announcement">
    <div class="tcm-container tcm-announcement-content">
      <span><strong>Institutional Partnerships 2026:</strong> Empower your engineering or BCA campus with outcome-based tech cohorts.</span>
      <a href="#partner-form">Submit Proposal &rarr;</a>
    </div>
  </div>

  <!-- Header Navigation -->
  <header class="tcm-header" role="banner">
    <div class="tcm-container">
      <div class="tcm-header-inner">
        <a href="<?= base_url('/') ?>" class="tcm-brand">
          <img src="<?= base_url('/assets/logo.png') ?>" alt="TCM One Logo" class="tcm-brand-logo">
          <span class="tcm-brand-text">TCM<span class="brand-accent">ONE</span></span>
        </a>

        <nav class="tcm-nav" aria-label="Main Navigation">
          <a href="<?= base_url('/courses') ?>" class="tcm-nav-link">Courses</a>
          <a href="<?= base_url('/workshops') ?>" class="tcm-nav-link">Workshops</a>
          <a href="<?= base_url('/for-institutions') ?>" class="tcm-nav-link active">For Institutions</a>
          <a href="<?= base_url('/about') ?>" class="tcm-nav-link">About</a>
          <a href="<?= base_url('/contact') ?>" class="tcm-nav-link">Contact</a>
        </nav>

        <div class="tcm-header-actions">
          <a href="<?= base_url('/login.php') ?>" class="tcm-btn tcm-btn-outline tcm-btn-sm tcm-auth-link d-none d-sm-inline-flex">Sign In</a>
          <a href="#partner-form" class="tcm-btn tcm-btn-primary tcm-btn-sm">
            <span>Partner With Us</span>
            <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
          </a>
          <button type="button" class="tcm-menu-toggle" aria-label="Toggle Navigation">
            <i class="bi bi-list" style="font-size: 22px;"></i>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Mobile Drawer Backdrop & Drawer -->
  <div class="tcm-drawer-backdrop" aria-hidden="true"></div>
  <aside class="tcm-mobile-drawer" aria-label="Mobile Menu">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
      <a href="<?= base_url('/') ?>" class="tcm-brand">
        <span class="tcm-brand-text">TCM<span class="brand-accent">ONE</span></span>
      </a>
      <button type="button" class="tcm-drawer-close" aria-label="Close Menu">&times;</button>
    </div>
    <nav style="display: flex; flex-direction: column; gap: 18px;">
      <a href="<?= base_url('/courses') ?>" class="tcm-nav-link" style="font-size: 16px;">Courses</a>
      <a href="<?= base_url('/workshops') ?>" class="tcm-nav-link" style="font-size: 16px;">Workshops</a>
      <a href="<?= base_url('/for-institutions') ?>" class="tcm-nav-link active" style="font-size: 16px;">For Institutions</a>
      <a href="<?= base_url('/about') ?>" class="tcm-nav-link" style="font-size: 16px;">About Us</a>
      <a href="<?= base_url('/contact') ?>" class="tcm-nav-link" style="font-size: 16px;">Contact</a>
    </nav>
    <div style="margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--tcm-border); display: flex; flex-direction: column; gap: 12px;">
      <a href="<?= base_url('/login.php') ?>" class="tcm-btn tcm-btn-secondary" style="width: 100%;">Sign In</a>
      <a href="#partner-form" class="tcm-btn tcm-btn-primary" style="width: 100%;">Partner With Us</a>
    </div>
  </aside>

  <main>
    <!-- Hero Section -->
    <section style="background: var(--tcm-surface); border-bottom: 1px solid var(--tcm-border); padding: 72px 0 64px; position: relative; overflow: hidden;">
      <div class="tcm-container">
        <div style="max-width: 840px;">
          <div class="tcm-eyebrow" style="margin-bottom: 14px;">ACADEMIC &amp; CAMPUS COLLABORATION</div>
          <h1 class="tcm-h1" style="font-size: clamp(34px, 5vw, 56px); line-height: 1.1; margin-bottom: 22px;">
            Bridge the Industry Gap for Your Engineering &amp; BCA Students.
          </h1>
          <p class="tcm-lead" style="font-size: 19px; line-height: 1.6; margin-bottom: 32px; color: var(--tcm-muted);">
            TCM One collaborates with universities, engineering colleges, and training institutes across India to deliver practical, project-first software education. Zero theory bloat. 100% production codebases.
          </p>

          <div style="display: flex; gap: 14px; flex-wrap: wrap;">
            <a href="#partner-form" class="tcm-btn tcm-btn-primary">
              <span>Submit Partnership Enquiry</span>
              <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
            </a>
            <a href="https://wa.me/919238695500?text=<?= rawurlencode('Hello TCM One! I am writing from an academic institution to discuss partnership opportunities.') ?>" target="_blank" rel="noopener" class="tcm-btn tcm-btn-secondary">
              <i class="bi bi-whatsapp"></i> Speak to Academic Lead
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Key Institutional Pillars Grid -->
    <section style="padding: 64px 0; border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="margin-bottom: 40px; max-width: 700px;">
          <div class="tcm-eyebrow" style="margin-bottom: 8px;">PEDAGOGY THAT DELIVERS</div>
          <h2 class="tcm-h2">Why Colleges Choose TCM One</h2>
          <p style="color: var(--tcm-muted); font-size: 15px; margin: 8px 0 0;">
            Most college students graduate with theoretical knowledge but struggle to write production-grade software. We solve this directly.
          </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
          <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: 28px;">
            <div style="font-size: 22px; margin-bottom: 14px; color: var(--tcm-fg);"><i class="bi bi-terminal"></i></div>
            <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Production Code Pedagogy</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Students learn using industry tools: Git, GitHub, modern terminal commands, Docker, and REST APIs. They graduate with live GitHub portfolios, not rote memorization.
            </p>
          </div>

          <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: 28px;">
            <div style="font-size: 22px; margin-bottom: 14px; color: var(--tcm-fg);"><i class="bi bi-person-video3"></i></div>
            <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Live Mentor Instruction</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Classes are taught live by practicing software engineers. Direct code reviews, interactive debugging sessions, and real-time Q&amp;A keep engagement exceptionally high.
            </p>
          </div>

          <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: 28px;">
            <div style="font-size: 22px; margin-bottom: 14px; color: var(--tcm-fg);"><i class="bi bi-mortarboard"></i></div>
            <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Faculty Development (FDP)</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Every partnership includes complimentary enablement workshops for your department faculty, keeping educators aligned with modern industry technology stacks.
            </p>
          </div>

          <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: 28px;">
            <div style="font-size: 22px; margin-bottom: 14px; color: var(--tcm-fg);"><i class="bi bi-qr-code-scan"></i></div>
            <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px;">Verifiable Credentials</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Each enrolled student receives a cryptographically verifiable completion certificate with dedicated verification URLs to showcase on LinkedIn and resumes.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- 3 Partnership Tracks -->
    <section style="padding: 72px 0; background: var(--tcm-surface); border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="margin-bottom: 48px; max-width: 760px;">
          <div class="tcm-eyebrow" style="margin-bottom: 8px;">OFFERINGS FOR CAMPUSES</div>
          <h2 class="tcm-h2">Curated Institutional Tracks</h2>
          <p style="color: var(--tcm-muted); font-size: 15px; margin: 8px 0 0;">
            Select the training format that best aligns with your college academic calendar and departmental objectives.
          </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 28px;">
          
          <!-- Track 1 -->
          <div class="inst-track-card">
            <div>
              <span class="tcm-badge" style="margin-bottom: 16px;">TRACK 01</span>
              <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 12px; font-family: 'Instrument Serif', Georgia, serif;">
                Semester Curriculum Integration
              </h3>
              <p style="font-size: 14px; color: var(--tcm-muted); line-height: 1.6; margin-bottom: 20px;">
                Comprehensive 40 to 80-hour hands-on tracks delivered across the academic semester. Covers Full Stack Web Development, Python &amp; Data Science, or Cloud &amp; DevOps.
              </p>
              <ul style="list-style: none; padding: 0; margin: 0 0 24px; display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: var(--tcm-fg);">
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Aligned with department timetable</li>
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Weekly assignments &amp; code reviews</li>
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Capstone project evaluation</li>
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> LMS &amp; student progress dashboards</li>
              </ul>
            </div>
            <a href="#partner-form" class="tcm-btn tcm-btn-secondary" style="width: 100%; justify-content: center;">
              Inquire for Semester Track
            </a>
          </div>

          <!-- Track 2 -->
          <div class="inst-track-card" style="border-top: 3px solid var(--tcm-fg);">
            <div>
              <span class="tcm-badge" style="margin-bottom: 16px; background: #111; color: #FFF;">TRACK 02 &middot; POPULAR</span>
              <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 12px; font-family: 'Instrument Serif', Georgia, serif;">
                Campus Bootcamps &amp; Hackathons
              </h3>
              <p style="font-size: 14px; color: var(--tcm-muted); line-height: 1.6; margin-bottom: 20px;">
                High-intensity 3 to 7-day technical immersion bootcamps. Students build real-world web apps or automation scrapers and showcase them in an intra-college hackathon.
              </p>
              <ul style="list-style: none; padding: 0; margin: 0 0 24px; display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: var(--tcm-fg);">
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> 3 to 7-day intensive format</li>
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Hands-on live coding every single day</li>
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Hackathon jury &amp; awards</li>
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Ideal for pre-placement preparation</li>
              </ul>
            </div>
            <a href="#partner-form" class="tcm-btn tcm-btn-primary" style="width: 100%; justify-content: center;">
              Book Campus Bootcamp
            </a>
          </div>

          <!-- Track 3 -->
          <div class="inst-track-card">
            <div>
              <span class="tcm-badge" style="margin-bottom: 16px;">TRACK 03</span>
              <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 12px; font-family: 'Instrument Serif', Georgia, serif;">
                Center of Excellence (CoE) MoU
              </h3>
              <p style="font-size: 14px; color: var(--tcm-muted); line-height: 1.6; margin-bottom: 20px;">
                Multi-year institutional partnership establishing a dedicated TCM One Software Engineering Hub inside your campus for continuous student upskilling.
              </p>
              <ul style="list-style: none; padding: 0; margin: 0 0 24px; display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: var(--tcm-fg);">
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Formal MoU with institutional accreditation</li>
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Continuous mentor-in-residence access</li>
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Direct recruitment drive linkages</li>
                <li><i class="bi bi-check2" style="margin-right: 6px;"></i> Ongoing faculty upskilling credits</li>
              </ul>
            </div>
            <a href="#partner-form" class="tcm-btn tcm-btn-secondary" style="width: 100%; justify-content: center;">
              Explore MoU Partnership
            </a>
          </div>

        </div>
      </div>
    </section>

    <!-- Delivery Process: 4 Clear Steps -->
    <section style="padding: 64px 0; border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="margin-bottom: 40px; max-width: 600px;">
          <div class="tcm-eyebrow" style="margin-bottom: 8px;">HOW IT WORKS</div>
          <h2 class="tcm-h2">Smooth 4-Step Rollout</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
          <div style="border-top: 1px solid var(--tcm-border); padding-top: 20px;">
            <div class="inst-step-num">01</div>
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">Needs Assessment</h3>
            <p style="font-size: 13px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              We review your department syllabus, student year, and lab infrastructure to map the ideal learning track.
            </p>
          </div>

          <div style="border-top: 1px solid var(--tcm-border); padding-top: 20px;">
            <div class="inst-step-num">02</div>
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">MoU &amp; Scheduling</h3>
            <p style="font-size: 13px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Sign the institutional agreement and set batch timetables that seamlessly fit into your academic calendar.
            </p>
          </div>

          <div style="border-top: 1px solid var(--tcm-border); padding-top: 20px;">
            <div class="inst-step-num">03</div>
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">Live Cohort Delivery</h3>
            <p style="font-size: 13px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Our engineers conduct live sessions, review student code repos, and resolve doubts with real-time feedback.
            </p>
          </div>

          <div style="border-top: 1px solid var(--tcm-border); padding-top: 20px;">
            <div class="inst-step-num">04</div>
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">Outcomes &amp; Showcase</h3>
            <p style="font-size: 13px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Students deploy capstone projects, earn verified credentials, and participate in placement readiness prep.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Institutional Lead Form Section -->
    <section id="partner-form" style="padding: 72px 0 88px; background: var(--tcm-surface);">
      <div class="tcm-container">
        <div class="inst-grid-2">
          
          <div>
            <div class="tcm-eyebrow" style="margin-bottom: 12px;">INITIATE PARTNERSHIP</div>
            <h2 class="tcm-h2" style="font-size: clamp(28px, 4vw, 42px); line-height: 1.15; margin-bottom: 18px;">
              Request a Custom Proposal for Your Campus
            </h2>
            <p style="color: var(--tcm-muted); font-size: 15px; line-height: 1.6; margin-bottom: 28px;">
              Fill in your institution details below. Our Academic Partnerships Director will connect with you within 24 hours with syllabus mapping and batch cost schedules.
            </p>

            <div style="background: var(--tcm-bg); border: 1px solid var(--tcm-border); padding: 24px; margin-bottom: 24px;">
              <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 10px;">Direct Academic Contacts</h4>
              <p style="font-size: 13.5px; color: var(--tcm-muted); margin: 0 0 8px;">
                <strong>WhatsApp / Phone:</strong> <a href="https://wa.me/919238695500" target="_blank" rel="noopener" style="color: var(--tcm-fg); font-weight: 600;">+91 9238695500</a>
              </p>
              <p style="font-size: 13.5px; color: var(--tcm-muted); margin: 0 0 8px;">
                <strong>Email:</strong> <a href="mailto:hello@thecodemunk.com" style="color: var(--tcm-fg);">hello@thecodemunk.com</a>
              </p>
              <p style="font-size: 12.5px; color: var(--tcm-muted); margin: 0;">
                Campus Visits: Available in Bengaluru &amp; Bhubaneswar
              </p>
            </div>
          </div>

          <!-- The Lead Form -->
          <div>
            <div style="background: var(--tcm-bg); border: 1px solid var(--tcm-border); padding: 36px;">
              
              <div id="instNotice" style="display: none;"></div>

              <form id="tcmInstitutionForm" novalidate>
                <div class="tcm-form-group">
                  <label for="instName" class="tcm-form-label">College / University Name *</label>
                  <input type="text" id="instName" class="tcm-form-input" placeholder="e.g. National Institute of Technology" required>
                </div>

                <div class="tcm-form-group">
                  <label for="instPerson" class="tcm-form-label">Contact Person &amp; Designation *</label>
                  <input type="text" id="instPerson" class="tcm-form-input" placeholder="e.g. Dr. A. Sharma (HOD - CSE / TPO)" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                  <div class="tcm-form-group">
                    <label for="instEmail" class="tcm-form-label">Official Email *</label>
                    <input type="email" id="instEmail" class="tcm-form-input" placeholder="e.g. hod.cse@college.edu" required>
                  </div>
                  <div class="tcm-form-group">
                    <label for="instPhone" class="tcm-form-label">Phone / Mobile *</label>
                    <input type="tel" id="instPhone" class="tcm-form-input" placeholder="e.g. 9876543210" maxlength="14" required>
                  </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                  <div class="tcm-form-group">
                    <label for="instCity" class="tcm-form-label">City &amp; State *</label>
                    <input type="text" id="instCity" class="tcm-form-input" placeholder="e.g. Bengaluru, Karnataka" required>
                  </div>
                  <div class="tcm-form-group">
                    <label for="instStudents" class="tcm-form-label">Expected Batch Size</label>
                    <select id="instStudents" class="tcm-form-input">
                      <option value="50-100 Students">50 &ndash; 100 Students</option>
                      <option value="100-250 Students" selected>100 &ndash; 250 Students</option>
                      <option value="250-500 Students">250 &ndash; 500 Students</option>
                      <option value="500+ Students">500+ Campus-wide</option>
                    </select>
                  </div>
                </div>

                <div class="tcm-form-group">
                  <label for="instModel" class="tcm-form-label">Partnership Track *</label>
                  <select id="instModel" class="tcm-form-input" required>
                    <option value="Semester Curriculum Integration" selected>Semester Curriculum Integration</option>
                    <option value="Campus Bootcamp & Hackathon">Campus Bootcamp &amp; Hackathon (3 to 7 Days)</option>
                    <option value="Center of Excellence (MoU)">Center of Excellence (MoU)</option>
                    <option value="Faculty Development Program (FDP)">Faculty Development Program (FDP)</option>
                  </select>
                </div>

                <div class="tcm-form-group">
                  <label for="instMessage" class="tcm-form-label">Additional Requirements or Preferred Dates</label>
                  <textarea id="instMessage" class="tcm-form-input" rows="3" placeholder="Tell us about your student year, key objectives, or target start month..."></textarea>
                </div>

                <button type="submit" class="tcm-btn tcm-btn-primary" id="instSubmitBtn" style="width: 100%; height: 50px;">
                  <span>Submit Partnership Enquiry</span>
                  <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
                </button>
              </form>

            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Institutional FAQ Accordion -->
    <section style="padding: 64px 0 80px; border-top: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="max-width: 760px; margin: 0 auto;">
          <div class="tcm-eyebrow" style="text-align: center; margin-bottom: 8px;">QUESTIONS &amp; ANSWERS</div>
          <h2 class="tcm-h2" style="text-align: center; margin-bottom: 36px;">Frequently Asked Questions by Institutions</h2>

          <div class="tcm-faq-item">
            <button type="button" class="tcm-faq-question">
              <span>What are the minimum computer lab requirements for students?</span>
              <span class="faq-icon">+</span>
            </button>
            <div class="tcm-faq-answer">
              <p>
                Standard college computer systems with modern browsers (Google Chrome or Firefox) and a stable internet connection are sufficient. All programming environments run on lightweight local setups or cloud browser IDEs (VS Code / GitHub Codespaces), requiring zero specialized server hardware.
              </p>
            </div>
          </div>

          <div class="tcm-faq-item">
            <button type="button" class="tcm-faq-question">
              <span>Can the sessions be scheduled within our regular college timetable?</span>
              <span class="faq-icon">+</span>
            </button>
            <div class="tcm-faq-answer">
              <p>
                Yes. We customize batch timings to match your timetable: weekday afternoon lab slots, dedicated Saturday morning cohorts, or intensive vacation bootcamps during semester breaks.
              </p>
            </div>
          </div>

          <div class="tcm-faq-item">
            <button type="button" class="tcm-faq-question">
              <span>How are student completion certificates verified by recruiters?</span>
              <span class="faq-icon">+</span>
            </button>
            <div class="tcm-faq-answer">
              <p>
                Each certificate carries a unique cryptographic ID and a QR code pointing to an online verification portal at TCM One. Recruiters and companies can immediately authenticate the student's capstone repository and verified skill breakdown.
              </p>
            </div>
          </div>

          <div class="tcm-faq-item">
            <button type="button" class="tcm-faq-question">
              <span>What is the typical lead time to initiate a cohort after MoU signing?</span>
              <span class="faq-icon">+</span>
            </button>
            <div class="tcm-faq-answer">
              <p>
                Once the institutional agreement is signed, student portal provisioning, lab checks, and instructor allocation are completed within 7 to 14 business days.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- Site Footer -->
  <footer class="tcm-footer" role="contentinfo">
    <div class="tcm-container">
      <div class="tcm-footer-grid">
        <div>
          <a href="<?= base_url('/') ?>" class="tcm-brand">
            <img src="<?= base_url('/assets/logo.png') ?>" alt="TCM One" class="tcm-brand-logo">
            <span class="tcm-brand-text">TCM<span class="brand-accent">ONE</span></span>
          </a>
          <p class="tcm-footer-brand-p">
            Practical tech education through live cohorts, real-world project portfolios, and direct mentorship.
          </p>
        </div>
        <div>
          <h4 class="tcm-footer-col-title">Platform</h4>
          <ul class="tcm-footer-links">
            <li><a href="<?= base_url('/courses') ?>">All Courses</a></li>
            <li><a href="<?= base_url('/workshops') ?>">Live Workshops</a></li>
            <li><a href="<?= base_url('/for-institutions') ?>">For Institutions</a></li>
            <li><a href="<?= base_url('/about') ?>">About Us</a></li>
            <li><a href="<?= base_url('/contact') ?>">Contact</a></li>
          </ul>
        </div>
        <div>
          <h4 class="tcm-footer-col-title">Admissions &amp; Partnerships</h4>
          <p style="font-size: 14px; color: var(--tcm-muted); margin: 0 0 12px 0;">
            WhatsApp: <a href="https://wa.me/919238695500" target="_blank" rel="noopener" style="color: var(--tcm-fg); font-weight: 600;">+91 9238695500</a>
          </p>
          <p style="font-size: 14px; color: var(--tcm-muted); margin: 0 0 12px 0;">
            Email: <a href="mailto:hello@thecodemunk.com" style="color: var(--tcm-fg);">hello@thecodemunk.com</a>
          </p>
          <p style="font-size: 12px; color: var(--tcm-muted); margin: 0;">
            Bengaluru &middot; Bhubaneswar, India
          </p>
        </div>
      </div>
      <div class="tcm-footer-bottom">
        <div>&copy; 2026 TCM One (The Code Munk). All rights reserved.</div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="<?= base_url('/assets/tcm-editorial.js') ?>"></script>
  <script src="<?= base_url('/assets/cai-assistant.js') ?>"></script>
</body>
</html>
