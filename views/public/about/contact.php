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

  <title><?= e($title ?? 'Contact Admissions & Mentors — TCM One') ?></title>
  <meta name="description" content="<?= e($description ?? 'Get in touch with TCM One. Ask questions about courses, workshops, fees, and enrollment.') ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical ?? 'https://thecodemunk.in/contact') ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:title" content="Contact Admissions &amp; Mentors &mdash; TCM One">
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
    .contact-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 40px;
    }
    @media (min-width: 992px) {
      .contact-grid {
        grid-template-columns: 1fr 1fr;
        gap: 56px;
      }
    }
    .contact-card-info {
      background: var(--tcm-surface);
      border: 1px solid var(--tcm-border);
      padding: 32px;
      margin-bottom: 24px;
    }
    .contact-quick-link {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px;
      border: 1px solid var(--tcm-border);
      background: var(--tcm-bg);
      text-decoration: none;
      color: var(--tcm-fg);
      margin-bottom: 12px;
      transition: all 0.15s ease;
    }
    .contact-quick-link:hover {
      border-color: var(--tcm-fg);
      background: var(--tcm-surface);
    }
  </style>
</head>
<body>

  <!-- Site Announcement -->
  <div class="tcm-announcement" role="region" aria-label="Announcement">
    <div class="tcm-container tcm-announcement-content">
      <span><strong>Direct Admissions Desk:</strong> Speak directly to our academic team on WhatsApp at +91 9238695500.</span>
      <a href="https://wa.me/919238695500?text=<?= rawurlencode('Hello TCM One! I have a question about courses.') ?>" target="_blank" rel="noopener">Open WhatsApp &rarr;</a>
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
          <a href="<?= base_url('/for-institutions') ?>" class="tcm-nav-link">For Institutions</a>
          <a href="<?= base_url('/about') ?>" class="tcm-nav-link">About</a>
          <a href="<?= base_url('/contact') ?>" class="tcm-nav-link active">Contact</a>
        </nav>

        <div class="tcm-header-actions">
          <a href="<?= base_url('/login.php') ?>" class="tcm-btn tcm-btn-outline tcm-btn-sm tcm-auth-link d-none d-sm-inline-flex">Sign In</a>
          <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary tcm-btn-sm">
            <span>Explore Courses</span>
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
      <a href="<?= base_url('/for-institutions') ?>" class="tcm-nav-link" style="font-size: 16px;">For Institutions</a>
      <a href="<?= base_url('/about') ?>" class="tcm-nav-link" style="font-size: 16px;">About Us</a>
      <a href="<?= base_url('/contact') ?>" class="tcm-nav-link active" style="font-size: 16px;">Contact</a>
    </nav>
    <div style="margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--tcm-border); display: flex; flex-direction: column; gap: 12px;">
      <a href="<?= base_url('/login.php') ?>" class="tcm-btn tcm-btn-secondary" style="width: 100%;">Sign In</a>
      <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary" style="width: 100%;">Explore Courses</a>
    </div>
  </aside>

  <main>
    <!-- Contact Hero -->
    <section style="background: var(--tcm-surface); border-bottom: 1px solid var(--tcm-border); padding: clamp(36px, 4.5vw, 52px) 0 clamp(24px, 3vw, 36px);">
      <div class="tcm-container">
        <div style="max-width: 820px;">
          <div class="tcm-eyebrow" style="margin-bottom: 12px;">ADMISSIONS &amp; SUPPORT</div>
          <h1 class="tcm-h1" style="font-size: clamp(34px, 5vw, 56px); line-height: 1.12; margin-bottom: 22px;">
            We're Here to Help You Choose the Right Path.
          </h1>
          <p class="tcm-lead" style="font-size: 19px; line-height: 1.6; color: var(--tcm-muted); margin: 0;">
            Have questions about upcoming cohort dates, syllabus depth, prerequisites, or fee structures? Send us a message or connect directly on WhatsApp. We reply within 24 hours.
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Content Layout -->
    <section style="padding: clamp(40px, 4.5vw, 56px) 0;">
      <div class="tcm-container">
        <div class="contact-grid">

          <!-- Left Column: Contact Channels & Locations -->
          <div>
            <div class="tcm-eyebrow" style="margin-bottom: 12px;">OFFICIAL CHANNELS</div>
            <h2 class="tcm-h2" style="font-size: 28px; margin-bottom: 20px;">
              Direct Access Points
            </h2>

            <!-- WhatsApp Quick Card -->
            <a href="https://wa.me/919238695500?text=<?= rawurlencode('Hello TCM One Admissions! I have an inquiry.') ?>" target="_blank" rel="noopener" class="contact-quick-link">
              <div style="font-size: 28px; color: #25D366;"><i class="bi bi-whatsapp"></i></div>
              <div>
                <strong style="display: block; font-size: 15px; margin-bottom: 2px;">Chat on WhatsApp</strong>
                <span style="font-size: 13px; color: var(--tcm-muted);">+91 9238695500 &middot; Fastest response (10 AM &ndash; 7 PM IST)</span>
              </div>
            </a>

            <!-- Email Quick Card -->
            <a href="mailto:hello@thecodemunk.com" class="contact-quick-link">
              <div style="font-size: 28px; color: var(--tcm-fg);"><i class="bi bi-envelope"></i></div>
              <div>
                <strong style="display: block; font-size: 15px; margin-bottom: 2px;">Admissions &amp; General Email</strong>
                <span style="font-size: 13px; color: var(--tcm-muted);">hello@thecodemunk.com &middot; Reply within 24 hours</span>
              </div>
            </a>

            <!-- Institutional Quick Card -->
            <a href="<?= base_url('/for-institutions') ?>" class="contact-quick-link">
              <div style="font-size: 28px; color: var(--tcm-fg);"><i class="bi bi-building"></i></div>
              <div>
                <strong style="display: block; font-size: 15px; margin-bottom: 2px;">College &amp; Campus Partnerships</strong>
                <span style="font-size: 13px; color: var(--tcm-muted);">Book campus workshops, hackathons &amp; MoU partnerships &rarr;</span>
              </div>
            </a>

            <!-- Office Locations Card -->
            <div class="contact-card-info" style="margin-top: 24px;">
              <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.05em;">
                Operational Hubs
              </h3>

              <div style="border-bottom: 1px solid var(--tcm-border); padding-bottom: 14px; margin-bottom: 14px;">
                <strong style="display: block; font-size: 14.5px; margin-bottom: 4px;">Bengaluru Tech Hub</strong>
                <p style="font-size: 13px; color: var(--tcm-muted); line-height: 1.5; margin: 0;">
                  Koramangala 4th Block, Bengaluru, Karnataka 560034, India
                </p>
              </div>

              <div>
                <strong style="display: block; font-size: 14.5px; margin-bottom: 4px;">Bhubaneswar Academic Hub</strong>
                <p style="font-size: 13px; color: var(--tcm-muted); line-height: 1.5; margin: 0;">
                  Infocity Avenue, Patia, Bhubaneswar, Odisha 751024, India
                </p>
              </div>
            </div>

            <div style="font-size: 12.5px; color: var(--tcm-muted); line-height: 1.5;">
              <i class="bi bi-info-circle"></i> We conduct 100% of student classes online through interactive live video sessions and real-time code environments.
            </div>
          </div>

          <!-- Right Column: Interactive Contact Form -->
          <div>
            <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: 36px;">
              <div class="tcm-eyebrow" style="margin-bottom: 8px;">SEND A MESSAGE</div>
              <h3 class="tcm-h3" style="margin-bottom: 18px;">Drop Us a Line</h3>
              
              <div id="ctNotice" style="display: none;"></div>

              <form id="tcmContactForm" novalidate>
                <div class="tcm-form-group">
                  <label for="ctName" class="tcm-form-label">Full Name *</label>
                  <input type="text" id="ctName" class="tcm-form-input" placeholder="e.g. Rahul Sharma" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                  <div class="tcm-form-group">
                    <label for="ctEmail" class="tcm-form-label">Email Address *</label>
                    <input type="email" id="ctEmail" class="tcm-form-input" placeholder="e.g. rahul@example.com" required>
                  </div>
                  <div class="tcm-form-group">
                    <label for="ctPhone" class="tcm-form-label">WhatsApp Number</label>
                    <input type="tel" id="ctPhone" class="tcm-form-input" placeholder="e.g. 9876543210" maxlength="14">
                  </div>
                </div>

                <div class="tcm-form-group">
                  <label for="ctSubject" class="tcm-form-label">Inquiry Topic *</label>
                  <select id="ctSubject" class="tcm-form-input" required>
                    <option value="Course Syllabus & Enrollment" selected>Course Syllabus &amp; Enrollment</option>
                    <option value="Workshop Registration Help">Workshop Registration Help</option>
                    <option value="Fee Structure & Payment">Fee Structure &amp; Payment Options</option>
                    <option value="College Partnership / MoU">College Partnership / MoU</option>
                    <option value="General Question">General Question</option>
                  </select>
                </div>

                <div class="tcm-form-group">
                  <label for="ctMessage" class="tcm-form-label">Your Message *</label>
                  <textarea id="ctMessage" class="tcm-form-input" rows="4" placeholder="How can we help? Tell us about your background or questions..." required></textarea>
                </div>

                <button type="submit" class="tcm-btn tcm-btn-primary" id="ctSubmitBtn" style="width: 100%; height: 50px;">
                  <span>Send Message</span>
                  <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
                </button>
              </form>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Contact FAQ Accordion -->
    <section style="padding: clamp(40px, 4.5vw, 56px) 0; background: var(--tcm-surface); border-top: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="max-width: 760px; margin: 0 auto;">
          <div class="tcm-eyebrow" style="text-align: center; margin-bottom: 8px;">HELPFUL INFORMATION</div>
          <h2 class="tcm-h2" style="text-align: center; margin-bottom: 36px;">Admissions &amp; Enrollment FAQ</h2>

          <div class="tcm-faq-item">
            <button type="button" class="tcm-faq-question">
              <span>How soon will I receive a reply after submitting?</span>
              <span class="faq-icon">+</span>
            </button>
            <div class="tcm-faq-answer">
              <p>
                Our admissions team monitors inquiries Monday through Saturday from 10:00 AM to 7:00 PM IST. You will typically receive an email or WhatsApp response within 2 to 4 business hours.
              </p>
            </div>
          </div>

          <div class="tcm-faq-item">
            <button type="button" class="tcm-faq-question">
              <span>Can I speak directly with a mentor before enrolling?</span>
              <span class="faq-icon">+</span>
            </button>
            <div class="tcm-faq-answer">
              <p>
                Yes! If you are deciding between different technical tracks, message our WhatsApp line (+91 9238695500) and request a 15-minute 1-on-1 counseling call with an engineering mentor to assess your current skill level and goals.
              </p>
            </div>
          </div>

          <div class="tcm-faq-item">
            <button type="button" class="tcm-faq-question">
              <span>What payment options do you support?</span>
              <span class="faq-icon">+</span>
            </button>
            <div class="tcm-faq-answer">
              <p>
                We accept all major Indian payment methods through Razorpay: UPI (GPay, PhonePe, Paytm), Net Banking, Debit Cards, and Credit Cards. For long-format courses priced at ₹5,000 or above, flexible 3-month no-cost EMI options are available at checkout.
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
          <h4 class="tcm-footer-col-title">Admissions &amp; Queries</h4>
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
