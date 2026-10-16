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

  <meta name="app-base" content="<?= e(base_url('/')) ?>">

  <title><?= e($title ?? 'Contact Admissions & Mentors — TCM One') ?></title>
  <meta name="description" content="<?= e($description ?? 'Get in touch with TCM One. Ask questions about courses, workshops, fees, and enrollment.') ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical ?? 'https://thecodemunk.in/contact') ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical ?? 'https://thecodemunk.in/contact') ?>">
  <meta property="og:title" content="Contact Admissions &amp; Mentors &mdash; TCM One">
  <meta property="og:description" content="<?= e($description ?? 'Get in touch with TCM One. Ask questions about courses, workshops, fees, and enrollment.') ?>">
  <meta property="og:image" content="<?= e($ogImage ?? base_url('/assets/logo.png')) ?>">
  <meta property="og:image:height" content="630">
  <meta property="og:locale" content="en_IN">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@thecodemunk">
  <meta name="twitter:title" content="Contact Admissions &amp; Mentors — TCM One">
  <meta name="twitter:description" content="<?= e($description ?? 'Get in touch with TCM One. Ask questions about courses, workshops, fees, and enrollment.') ?>">
  <meta name="twitter:image" content="<?= e($ogImage ?? 'https://thecodemunk.in/assets/logo.png') ?>">

  <!-- International Hreflang Tags -->
  <link rel="alternate" hreflang="en-IN" href="<?= e($canonical ?? 'https://thecodemunk.in/contact') ?>">
  <link rel="alternate" hreflang="en-US" href="<?= e($canonical ?? 'https://thecodemunk.in/contact') ?>">
  <link rel="alternate" hreflang="x-default" href="<?= e($canonical ?? 'https://thecodemunk.in/contact') ?>">

  <!-- Structured Data (JSON-LD) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ContactPage",
    "name": "Contact Admissions & Mentors — TCM One",
    "description": "Direct communication channels for student queries, course admissions, syllabus counseling, and college partnerships.",
    "url": "https://thecodemunk.in/contact",
    "mainEntity": {
      "@type": "EducationalOrganization",
      "name": "TCM One",
      "alternateName": "The Code Munk",
      "url": "https://thecodemunk.in",
      "telephone": "+91-9238695500",
      "email": "thecodemunk@gmail.com",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Seepat Rd, near DLS college, Shree Vihar, Ashok Nagar, Sarkanda",
        "addressLocality": "Bilaspur",
        "addressRegion": "Chhattisgarh",
        "postalCode": "495006",
        "addressCountry": "IN"
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+91-9238695500",
        "contactType": "admissions",
        "areaServed": ["IN", "US", "AE", "CA", "GB"],
        "availableLanguage": ["English", "Hindi"]
      }
    }
  }
  </script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "https://thecodemunk.in/"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Contact",
        "item": "https://thecodemunk.in/contact"
      }
    ]
  }
  </script>

  <!-- Typography & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Core Bootstrap 5 CSS for Grid & Utilities -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Design System Stylesheets -->
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/cai-assistant.css?v=2.18') ?>">

  <style>
    *, *::before, *::after {
      border-radius: 0px !important;
    }

    body {
      background-color: var(--tcm-bg);
      color: var(--tcm-fg);
      font-family: var(--tcm-font-sans);
      margin: 0;
      padding: 0;
    }

    .contact-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 40px;
      align-items: start;
    }
    .contact-grid > *, .contact-form-row > * { min-width: 0; }
    .contact-form-card {
      background: #FFFFFF;
      border: 1px solid var(--tcm-border);
      padding: clamp(24px, 3.5vw, 40px);
    }
    .contact-form-row { display: grid; grid-template-columns: 1fr; gap: 0 16px; }
    #contact-faq { scroll-margin-top: 90px; }
    @media (min-width: 600px) {
      .contact-form-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 992px) {
      .contact-grid {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 56px;
      }
    }
    .contact-card-info {
      background: #FFFFFF;
      border: 1px solid var(--tcm-border);
      padding: 28px;
      margin-bottom: 24px;
    }
    .contact-quick-link {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 18px 20px;
      border: 1px solid var(--tcm-border);
      background: #FFFFFF;
      text-decoration: none;
      color: var(--tcm-fg);
      margin-bottom: 12px;
      transition: border-color 0.15s ease, transform 0.15s ease;
    }
    .contact-quick-link:hover {
      border-color: var(--tcm-fg);
      color: var(--tcm-fg);
      transform: translateY(-1px);
    }
    .contact-quick-link > :first-child { flex-shrink: 0; }
    .contact-quick-link > :last-child { min-width: 0; overflow-wrap: anywhere; }
  </style>
</head>
<body class="tcm-editorial">

  <!-- Header Navigation & Mobile Drawer -->
  <?php \TCM\Core\View::partial('partials.public-header'); ?>

  <main id="main-content">
    <!-- Contact Hero -->
    <section style="background: var(--tcm-bg); border-bottom: 1px solid var(--tcm-border); padding: clamp(48px, 6vw, 76px) 0 clamp(32px, 4vw, 52px);">
      <div class="tcm-container">
        <div style="max-width: 820px;">
          <div class="tcm-eyebrow">ADMISSIONS &amp; DIRECT SUPPORT</div>
          <h1 class="tcm-h1" style="margin: 8px 0 18px 0; font-size: clamp(2.4rem, 4.8vw, 3.8rem); line-height: 1.12;">
            We're Here to Help You Choose the <span class="font-editorial-serif">Right Path.</span>
          </h1>
          <p class="tcm-lead" style="font-size: 17.5px; line-height: 1.65; color: var(--tcm-muted); margin: 0;">
            Have questions about upcoming cohort dates, syllabus depth, prerequisites, or fee structures? Send us a message or connect directly on WhatsApp. We reply within 24 hours.
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Content Layout -->
    <section style="padding: clamp(48px, 6vw, 72px) 0; background: #FAFAFA;">
      <div class="tcm-container">
        <div class="contact-grid">

          <!-- Left Column: Contact Channels & Locations -->
          <div>
            <div class="tcm-eyebrow">OFFICIAL CHANNELS</div>
            <h2 class="tcm-h2" style="margin: 8px 0 20px 0;">
              Direct Access <span class="font-editorial-serif">Points</span>
            </h2>

            <!-- WhatsApp Quick Card -->
            <a href="https://wa.me/919238695500?text=<?= rawurlencode('Hello TCM One Admissions! I have an inquiry.') ?>" target="_blank" rel="noopener" class="contact-quick-link">
              <div style="font-size: 26px; color: var(--tcm-fg);"><i class="bi bi-whatsapp"></i></div>
              <div>
                <strong style="display: block; font-size: 15px; margin-bottom: 2px;">Chat on WhatsApp</strong>
                <span style="font-size: 13px; color: var(--tcm-muted);">+91 9238695500 &middot; Fastest response (10 AM &ndash; 7 PM IST)</span>
              </div>
            </a>

            <!-- Email Quick Card -->
            <a href="mailto:thecodemunk@gmail.com" class="contact-quick-link">
              <div style="font-size: 26px; color: var(--tcm-fg);"><i class="bi bi-envelope"></i></div>
              <div>
                <strong style="display: block; font-size: 15px; margin-bottom: 2px;">Admissions &amp; General Email</strong>
                <span style="font-size: 13px; color: var(--tcm-muted);">thecodemunk@gmail.com &middot; Reply within 24 hours</span>
              </div>
            </a>

            <!-- Institutional Quick Card -->
            <a href="<?= base_url('/for-institutions') ?>" class="contact-quick-link">
              <div style="font-size: 26px; color: var(--tcm-fg);"><i class="bi bi-building"></i></div>
              <div>
                <strong style="display: block; font-size: 15px; margin-bottom: 2px;">College &amp; Campus Partnerships</strong>
                <span style="font-size: 13px; color: var(--tcm-muted);">Campus workshops, practical curricula &amp; MoU partnerships &rarr;</span>
              </div>
            </a>

            <!-- Office Locations Card -->
            <div class="contact-card-info" style="margin-top: 24px;">
              <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--tcm-muted); margin-bottom: 16px;">
                HEADQUARTERS &amp; LEARNING CAMPUS
              </div>

              <div style="border-bottom: 1px solid var(--tcm-border); padding-bottom: 16px; margin-bottom: 16px;">
                <strong style="display: block; font-size: 15px; margin-bottom: 6px; color: var(--tcm-fg);">Bilaspur Main Campus</strong>
                <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.6; margin: 0 0 10px;">
                  Seepat Rd, near DLS college, Shree Vihar, Ashok Nagar, Sarkanda, Bilaspur, Chhattisgarh 495006, India
                </p>
                <a href="https://www.google.com/maps/search/?api=1&query=Seepat+Rd,+near+DLS+college,+Shree+Vihar,+Ashok+Nagar,+Sarkanda,+Bilaspur,+Chhattisgarh+495006" target="_blank" rel="noopener" class="tcm-btn tcm-btn-outline tcm-btn-sm" style="font-size: 12px; display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px;">
                  <i class="bi bi-geo-alt-fill" style="color: var(--tcm-accent, #c5f467);"></i> View on Google Maps &rarr;
                </a>
              </div>

              <div>
                <strong style="display: block; font-size: 14.5px; margin-bottom: 4px; color: var(--tcm-fg);">Online Live Cohorts</strong>
                <p style="font-size: 13px; color: var(--tcm-muted); line-height: 1.5; margin: 0;">
                  Live interactive coding, screen debugging, and mentor office hours accessible across India and worldwide.
                </p>
              </div>

              <div style="margin-top: 14px; border: 1px solid var(--tcm-border); overflow: hidden; height: 160px;">
                <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=Seepat+Rd,+near+DLS+college,+Shree+Vihar,+Ashok+Nagar,+Sarkanda,+Bilaspur,+Chhattisgarh+495006&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
              </div>
            </div>

            <div style="font-size: 12.5px; color: var(--tcm-muted); line-height: 1.6; padding: 14px; background: #FFFFFF; border: 1px solid var(--tcm-border);">
              <i class="bi bi-info-circle me-1"></i> <strong>Online &amp; In-Person Delivery:</strong> We conduct live online cohort classes accessible from anywhere, alongside hands-on practical sessions and bootcamps at our Bilaspur campus.
            </div>
          </div>

          <!-- Right Column: Interactive Contact Form -->
          <div>
            <div class="contact-form-card">
              <div class="tcm-eyebrow" style="margin-bottom: 8px;">SEND A MESSAGE</div>
              <h3 class="tcm-h3" style="margin-bottom: 18px; font-size: 26px;">Drop Us a <span class="font-editorial-serif">Line</span></h3>
              
              <div id="ctNotice" role="status" aria-live="polite" style="display: none;"></div>

              <form id="tcmContactForm" novalidate>
                <div class="tcm-form-group" style="margin-bottom: 18px;">
                  <label for="ctName" class="tcm-form-label" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">Full Name *</label>
                  <input type="text" id="ctName" name="name" autocomplete="name" class="tcm-form-input" placeholder="e.g. Rahul Sharma" required style="width: 100%; height: 44px; padding: 10px 14px; border: 1px solid var(--tcm-border); background: #FFFFFF; font-size: 14px;">
                </div>

                <div class="contact-form-row" style="margin-bottom: 18px;">
                  <div class="tcm-form-group">
                    <label for="ctEmail" class="tcm-form-label" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">Email Address *</label>
                    <input type="email" id="ctEmail" name="email" autocomplete="email" class="tcm-form-input" placeholder="e.g. rahul@example.com" required style="width: 100%; height: 44px; padding: 10px 14px; border: 1px solid var(--tcm-border); background: #FFFFFF; font-size: 14px;">
                  </div>
                  <div class="tcm-form-group">
                    <label for="ctPhone" class="tcm-form-label" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">WhatsApp Number</label>
                    <input type="tel" id="ctPhone" name="phone" autocomplete="tel" class="tcm-form-input" placeholder="e.g. 9876543210" maxlength="14" style="width: 100%; height: 44px; padding: 10px 14px; border: 1px solid var(--tcm-border); background: #FFFFFF; font-size: 14px;">
                  </div>
                </div>

                <div class="tcm-form-group" style="margin-bottom: 18px;">
                  <label for="ctSubject" class="tcm-form-label" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">Inquiry Topic *</label>
                  <select id="ctSubject" name="subject" class="tcm-form-select" required style="width: 100%; height: 44px; padding: 10px 14px; border: 1px solid var(--tcm-border); background: #FFFFFF; font-size: 14px;">
                    <option value="Course Syllabus & Enrollment" selected>Course Syllabus &amp; Enrollment</option>
                    <option value="Workshop Registration Help">Workshop Registration Help</option>
                    <option value="Fee Structure & Payment">Fee Structure &amp; Payment Options</option>
                    <option value="College Partnership / MoU">College Partnership / MoU</option>
                    <option value="General Question">General Question</option>
                  </select>
                </div>

                <div class="tcm-form-group" style="margin-bottom: 24px;">
                  <label for="ctMessage" class="tcm-form-label" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">Your Message *</label>
                  <textarea id="ctMessage" name="message" class="tcm-form-textarea" rows="5" minlength="5" placeholder="How can we help? Tell us about your technical background or questions..." required style="width: 100%; padding: 12px 14px; border: 1px solid var(--tcm-border); background: #FFFFFF; font-size: 14px;"></textarea>
                </div>

                <button type="submit" class="tcm-btn tcm-btn-primary" id="ctSubmitBtn" style="width: 100%; height: 46px; justify-content: center;">
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
    <section id="contact-faq" aria-labelledby="contact-faq-title" style="padding: clamp(48px, 6vw, 72px) 0; background: var(--tcm-bg); border-top: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="max-width: 760px; margin: 0 auto;">
          <div class="tcm-eyebrow" style="text-align: center; margin-bottom: 8px;">HELPFUL INFORMATION</div>
          <h2 id="contact-faq-title" class="tcm-h2" style="text-align: center; margin-bottom: 36px;">
            Admissions &amp; Enrollment <span class="font-editorial-serif">FAQ</span>
          </h2>

          <div class="tcm-faq-list" style="margin-top: 0;">
            <details class="tcm-faq-item" name="contact-faq" open>
              <summary class="tcm-faq-summary">
                <span>How soon will I receive a reply after submitting?</span>
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
              </summary>
              <div class="tcm-faq-body">
                <p>
                  Our admissions team monitors inquiries Monday through Saturday from 10:00 AM to 7:00 PM IST. You will typically receive an email or WhatsApp response during support hours, usually within one business day.
                </p>
              </div>
            </details>

            <details class="tcm-faq-item" name="contact-faq">
              <summary class="tcm-faq-summary">
                <span>Can I speak directly with a mentor before enrolling?</span>
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
              </summary>
              <div class="tcm-faq-body">
                <p>
                  Yes! If you are deciding between different technical tracks, message our WhatsApp line (+91 9238695500) and request a 15-minute 1-on-1 counseling call with an engineering mentor to assess your current skill level and goals.
                </p>
              </div>
            </details>

            <details class="tcm-faq-item" name="contact-faq">
              <summary class="tcm-faq-summary">
                <span>What payment options do you support?</span>
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
              </summary>
              <div class="tcm-faq-body">
                <p>
                  Payment methods are those offered through official TCM payment instructions at enrolment. The current student flow accepts a payment reference or receipt for manual verification before access is activated. Gateway payment methods and any instalment arrangement are available only when expressly offered; please confirm with admissions before paying.</p>
              </div>
            </details>
          </div>
        </div>
      </div>
    </section>
  <?php readfile(BASE_PATH . '/views/partials/business-identity.html'); ?>
</main>

  <!-- Site Footer -->
  <?php \TCM\Core\View::partial('partials.public-footer'); ?>

  <!-- Scripts -->
  <script src="<?= base_url('/assets/tcm-editorial.js?v=2.18') ?>"></script>
  <script src="<?= base_url('/assets/cai-assistant.js?v=2.18') ?>"></script>
</body>
</html>
