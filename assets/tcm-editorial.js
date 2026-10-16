/**
 * TCM One — Premium Editorial Controller & Dynamic Hydration Engine
 * Interacts with existing backend APIs:
 *   - /api/courses
 *   - /api/courses/{slug}
 *   - /api/workshops/featured
 *   - /api/workshops/register
 *   - /api/courses/brochure-lead
 *   - /api/demo-lead
 *   - /api/contact
 *   - /api/partner/apply
 *   - /api/me
 */
(function () {
  'use strict';

  var BASE = (function () {
    if (typeof window !== 'undefined' && window.TCM_BASE != null) {
      return String(window.TCM_BASE).replace(/\/$/, '');
    }
    var meta = document.querySelector('meta[name="app-base"]');
    if (meta && meta.content !== '') {
      var val = meta.content.trim();
      if (/^https?:\/\//i.test(val)) {
        try {
          var u = new URL(val);
          if (typeof window !== 'undefined' && window.location && window.location.origin) {
            if (u.origin === window.location.origin) {
              return u.pathname.replace(/\/$/, '');
            }
          }
        } catch (e) {}
      } else {
        return val.replace(/\/$/, '');
      }
    }
    // Auto-detect from script URL tag (handles /tcm on XAMPP)
    if (typeof document !== 'undefined') {
      var scripts = document.getElementsByTagName('script');
      for (var i = 0; i < scripts.length; i++) {
        var src = scripts[i].src || '';
        var idx = src.indexOf('/assets/tcm-editorial.js');
        if (idx !== -1) {
          try {
            var urlObj = new URL(src);
            var subPath = urlObj.pathname.substring(0, urlObj.pathname.indexOf('/assets/tcm-editorial.js'));
            if (subPath) return subPath.replace(/\/$/, '');
          } catch (e) {}
        }
      }
    }
    // Auto-detect from window.location.pathname on localhost
    if (typeof window !== 'undefined' && window.location && window.location.pathname) {
      var match = window.location.pathname.match(/^(\/tcm)(?:\/|$)/i);
      if (match) {
        return match[1];
      }
    }
    return '';
  })();

  function normalizeSiteLinks() {
    if (!BASE) return;
    var links = document.querySelectorAll('a[href^="/"]');
    links.forEach(function (a) {
      var href = a.getAttribute('href');
      if (href && href.indexOf(BASE + '/') !== 0 && href !== BASE) {
        if (href === '/') {
          a.setAttribute('href', BASE + '/');
        } else if (href.indexOf('//') !== 0) {
          a.setAttribute('href', BASE + href);
        }
      }
    });
  }

  // Intercept any clicks on root-relative links to guarantee navigation stays inside BASE
  if (typeof document !== 'undefined') {
    document.addEventListener('click', function (e) {
      if (!BASE) return;
      var a = e.target && e.target.closest ? e.target.closest('a') : null;
      if (!a) return;
      var href = a.getAttribute('href');
      if (href && href.charAt(0) === '/' && href.indexOf('//') !== 0) {
        if (href.indexOf(BASE + '/') !== 0 && href !== BASE) {
          e.preventDefault();
          var targetUrl = (href === '/') ? (BASE + '/') : (BASE + href);
          window.location.href = targetUrl;
        }
      }
    }, false);
  }

  function apiUrl(p) {
    return BASE + p;
  }

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function formatMoney(n) {
    var val = Number(n || 0);
    return '₹' + val.toLocaleString('en-IN');
  }

  function formatUsd(n) {
    var inr = Number(n || 0);
    if (!inr) return '$0 USD';
    var usd = Math.round(inr / 83.5);
    return '$' + usd.toLocaleString('en-US') + ' USD';
  }

  function getJSON(url) {
    return fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
      .then(function (r) { return r.json(); });
  }

  function postJSON(url, data) {
    var token = (window.TCM_USER && window.TCM_USER.csrf_token) || '';
    return fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-Token': token
      },
      credentials: 'same-origin',
      body: JSON.stringify(data)
    }).then(function (r) {
      return r.json().catch(function () {
        return { success: false, message: 'Server communication error.' };
      });
    });
  }

  function trackEvent(name, data) {
    try {
      if (typeof window.gtag === 'function') {
        var payload = data || {};
        payload.page_location = window.location.href;
        window.gtag('event', name, payload);
      }
    } catch (e) {}
  }

  /* ==========================================================================
     MOBILE NAVIGATION DRAWER
     ========================================================================== */
  function wireNavigation() {
    var toggle = document.querySelector('.tcm-menu-toggle');
    var drawer = document.querySelector('.tcm-mobile-drawer');
    var backdrop = document.querySelector('.tcm-drawer-backdrop');
    var closeBtn = document.querySelector('.tcm-drawer-close');

    function openDrawer() {
      if (drawer) drawer.classList.add('open');
      if (backdrop) backdrop.classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
      if (drawer) drawer.classList.remove('open');
      if (backdrop) backdrop.classList.remove('open');
      document.body.style.overflow = '';
    }

    if (toggle) toggle.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (backdrop) backdrop.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && drawer && drawer.classList.contains('open')) {
        closeDrawer();
      }
    });
  }

  /* ==========================================================================
     COURSE CARD GENERATOR (3 Col Desktop, 2 Col Tablet, 1 Col Mobile)
     ========================================================================== */
  function renderCourseCard(c) {
    var priceInr = Number(c.price || 0);
    var origInr = c.original_price ? Number(c.original_price) : 0;
    var slug = c.slug || '';
    var courseUrl = apiUrl('/courses/' + encodeURIComponent(slug));
    var category = c.category_name || c.category || 'General';
    var level = c.level ? (c.level.charAt(0).toUpperCase() + c.level.slice(1)) : 'All Levels';
    var duration = c.duration || '6 Weeks';

    return (
      '<div class="tcm-course-card" data-slug="' + esc(slug) + '" data-cat="' + esc(category.toLowerCase()) + '" data-price="' + priceInr + '" data-level="' + esc(level.toLowerCase()) + '">' +
        '<div class="tcm-course-top">' +
          '<span class="tcm-course-cat">' + esc(category) + '</span>' +
          '<span class="tcm-course-level">' + esc(level) + '</span>' +
        '</div>' +
        '<h3 class="tcm-course-title">' +
          '<a href="' + esc(courseUrl) + '">' + esc(c.title) + '</a>' +
        '</h3>' +
        '<p class="tcm-course-desc">' + esc(c.subtitle || c.description || '') + '</p>' +
        '<div class="tcm-course-meta">' +
          '<span class="tcm-course-meta-item"><i class="bi bi-clock"></i> ' + esc(duration) + '</span>' +
          '<span class="tcm-course-meta-item"><i class="bi bi-broadcast"></i> Live Cohort</span>' +
        '</div>' +
        '<div class="tcm-course-bottom">' +
          '<div class="tcm-course-price-wrap">' +
            '<div>' +
              '<span class="tcm-course-price">' + formatMoney(priceInr) + '</span>' +
              (origInr ? '<span class="tcm-course-orig-price">' + formatMoney(origInr) + '</span>' : '') +
            '</div>' +
            '<span class="tcm-course-usd">' + formatUsd(priceInr) + '</span>' +
          '</div>' +
          '<a href="' + esc(courseUrl) + '" class="tcm-btn tcm-btn-secondary tcm-btn-sm">' +
            '<span>View Course</span>' +
            '<svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>' +
          '</a>' +
        '</div>' +
      '</div>'
    );
  }

  /* ==========================================================================
     HOMEPAGE DYNAMIC HYDRATION
     ========================================================================== */
  function hydrateHomeCourses() {
    var container = document.getElementById('featuredCoursesGrid');
    if (!container) return;

    getJSON(apiUrl('/api/courses?featured=1'))
      .then(function (res) {
        if (!res || !res.success || !Array.isArray(res.data) || !res.data.length) {
          // Fallback to general course list if featured flag yields 0
          return getJSON(apiUrl('/api/courses'));
        }
        return res;
      })
      .then(function (res) {
        if (!res || !res.success || !Array.isArray(res.data) || !res.data.length) {
          container.innerHTML = '<p class="tcm-body-muted">Courses are currently being updated for the next cohort.</p>';
          return;
        }
        var courses = res.data.slice(0, 6);
        container.innerHTML = courses.map(renderCourseCard).join('');
      })
      .catch(function () {
        container.innerHTML = '<p class="tcm-body-muted">Unable to load featured courses at this time.</p>';
      });
  }

  function renderCategoryCard(cat, index) {
    var num = String(index + 1).padStart(2, '0');
    var icon = cat.icon || 'bi-layers';
    var catUrl = cat.url || apiUrl('/courses?cat=' + encodeURIComponent(cat.name));
    var tagsHtml = (cat.tags || []).slice(0, 5).map(function (t) {
      return '<span class="tcm-track-tag">' + esc(t) + '</span>';
    }).join('');

    return (
      '<a href="' + catUrl + '" class="tcm-track-card">' +
        '<div>' +
          '<div class="tcm-track-top">' +
            '<span class="tcm-track-index">TRACK ' + num + '</span>' +
            '<span class="tcm-track-count-badge">' + cat.course_count + ' Programs</span>' +
          '</div>' +
          '<i class="bi ' + esc(icon) + ' tcm-track-icon"></i>' +
          '<h3 class="tcm-track-title">' + esc(cat.name) + '</h3>' +
          '<p class="tcm-track-desc">' + esc(cat.description || '') + '</p>' +
          '<div class="tcm-track-tags">' + tagsHtml + '</div>' +
        '</div>' +
        '<div class="tcm-track-bottom">' +
          '<span>From ' + esc(cat.formatted_min_price || ('₹' + Number(cat.min_price || 0))) + '</span>' +
          '<span class="tcm-track-arrow">Explore Track &rarr;</span>' +
        '</div>' +
      '</a>'
    );
  }

  function hydrateHomeCategoryTracks() {
    var container = document.getElementById('homeCategoryTracksGrid');
    if (!container) return;

    getJSON(apiUrl('/api/categories'))
      .then(function (res) {
        if (!res || !res.success || !Array.isArray(res.data) || !res.data.length) return;
        container.innerHTML = res.data.map(renderCategoryCard).join('');
      })
      .catch(function () {});
  }

  /* ==========================================================================
     COURSES CATALOG (PAGE 2 — /courses) WITH SEARCH & FILTERS
     ========================================================================== */
  function initCourseCatalog() {
    var grid = document.getElementById('courseCatalogGrid');
    var countEl = document.getElementById('catalogCount');
    var searchInput = document.getElementById('catalogSearch');
    var catSelect = document.getElementById('catalogCategory');
    var levelSelect = document.getElementById('catalogLevel');
    var priceSelect = document.getElementById('catalogPrice');
    var sortSelect = document.getElementById('catalogSort');
    var catPillsWrap = document.getElementById('catalogCategoryPills');
    var levelPillsWrap = document.getElementById('catalogLevelPills');
    var filterToggle = document.getElementById('catalogFilterToggle');
    var filterPanel = document.getElementById('catalogFilterPanel');
    var filterCount = document.getElementById('catalogFilterCount');
    var filterDone = document.getElementById('catalogFilterDone');
    var filterReset = document.getElementById('catalogResetFilters');

    if (!grid) return;

    var allCourses = [];

    function setFiltersOpen(open, restoreFocus) {
      if (!filterToggle || !filterPanel) return;
      filterPanel.hidden = !open;
      filterToggle.setAttribute('aria-expanded', String(open));
      if (restoreFocus) filterToggle.focus();
    }

    if (filterToggle && filterPanel) {
      filterToggle.addEventListener('click', function () {
        setFiltersOpen(filterPanel.hidden, false);
      });
      filterPanel.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          e.preventDefault();
          setFiltersOpen(false, true);
        }
      });
    }
    if (filterDone) filterDone.addEventListener('click', function () { setFiltersOpen(false, true); });
    if (filterReset) filterReset.addEventListener('click', resetCatalogFilters);

    function updateFilterSummary() {
      var count = [catSelect, levelSelect, priceSelect].filter(function (select) {
        return select && select.value;
      }).length;
      if (filterCount) {
        filterCount.textContent = String(count);
        filterCount.hidden = count === 0;
      }
      [catPillsWrap, levelPillsWrap].forEach(function (group) {
        if (!group) return;
        group.querySelectorAll('.tcm-pill-btn').forEach(function (pill) {
          pill.setAttribute('aria-pressed', String(pill.classList.contains('active')));
        });
      });
    }

    function resetCatalogFilters() {
      if (searchInput) searchInput.value = '';
      if (priceSelect) priceSelect.value = '';
      if (sortSelect) sortSelect.value = 'relevance';
      selectCategoryValue('');
      selectLevelValue('');
      syncUrlParams();
      applyFilters();
    }

    // Parse URL query parameters
    var params = new URLSearchParams(window.location.search);
    if (params.get('q') && searchInput) searchInput.value = params.get('q');
    
    function selectCategoryValue(val) {
      var target = String(val || '').trim().toLowerCase();
      var slugTarget = target.replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
      if (catSelect) {
        for (var i = 0; i < catSelect.options.length; i++) {
          var opt = catSelect.options[i];
          var optVal = opt.value.trim().toLowerCase();
          var optSlug = optVal.replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
          if ((!target && !optVal) || (target && (optVal === target || optSlug === slugTarget || optVal.indexOf(target) !== -1))) {
            catSelect.selectedIndex = i;
            break;
          }
        }
      }
      if (catPillsWrap) {
        var pills = catPillsWrap.querySelectorAll('.tcm-pill-btn');
        var matched = false;
        pills.forEach(function (pill) {
          var pVal = (pill.getAttribute('data-value') || '').trim().toLowerCase();
          var pSlug = pVal.replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
          if ((!target && !pVal) || (target && (pVal === target || pSlug === slugTarget || pVal.indexOf(target) !== -1))) {
            pill.classList.add('active');
            matched = true;
          } else {
            pill.classList.remove('active');
          }
        });
        if (!matched && pills.length && !target) {
          pills[0].classList.add('active');
        }
      }
    }

    function selectLevelValue(val) {
      var target = String(val || '').trim().toLowerCase();
      if (levelSelect) levelSelect.value = target;
      if (levelPillsWrap) {
        var pills = levelPillsWrap.querySelectorAll('.tcm-pill-btn-sm');
        pills.forEach(function (pill) {
          var pVal = (pill.getAttribute('data-value') || '').trim().toLowerCase();
          if ((!target && !pVal) || (target && pVal === target)) {
            pill.classList.add('active');
          } else {
            pill.classList.remove('active');
          }
        });
      }
    }

    if (params.get('cat')) selectCategoryValue(params.get('cat'));
    if (params.get('level')) selectLevelValue(params.get('level'));
    if (params.get('price') && priceSelect) priceSelect.value = params.get('price');
    if (params.get('sort') && sortSelect) sortSelect.value = params.get('sort');

    function syncUrlParams() {
      var nextParams = new URLSearchParams();
      if (searchInput && searchInput.value.trim()) nextParams.set('q', searchInput.value.trim());

      var catVal = '';
      if (catPillsWrap) {
        var activeCatPill = catPillsWrap.querySelector('.tcm-pill-btn.active');
        if (activeCatPill) catVal = (activeCatPill.getAttribute('data-value') || '').trim();
      } else if (catSelect) {
        catVal = catSelect.value.trim();
      }
      if (catVal) nextParams.set('cat', catVal);

      var lvlVal = '';
      if (levelPillsWrap) {
        var activeLvlPill = levelPillsWrap.querySelector('.tcm-pill-btn-sm.active');
        if (activeLvlPill) lvlVal = (activeLvlPill.getAttribute('data-value') || '').trim();
      } else if (levelSelect) {
        lvlVal = levelSelect.value.trim();
      }
      if (lvlVal) nextParams.set('level', lvlVal);

      if (priceSelect && priceSelect.value) nextParams.set('price', priceSelect.value);
      if (sortSelect && sortSelect.value && sortSelect.value !== 'relevance') nextParams.set('sort', sortSelect.value);

      var newSearch = nextParams.toString();
      var newUrl = window.location.pathname + (newSearch ? '?' + newSearch : '');
      window.history.replaceState({}, '', newUrl);
    }

    function applyFilters() {
      updateFilterSummary();
      if (!allCourses.length) return;

      var q = searchInput ? searchInput.value.trim().toLowerCase() : '';

      var cat = '';
      if (catPillsWrap) {
        var activeCatPill = catPillsWrap.querySelector('.tcm-pill-btn.active');
        if (activeCatPill) cat = (activeCatPill.getAttribute('data-value') || '').trim().toLowerCase();
      } else if (catSelect) {
        cat = catSelect.value.trim().toLowerCase();
      }

      var lvl = '';
      if (levelPillsWrap) {
        var activeLvlPill = levelPillsWrap.querySelector('.tcm-pill-btn-sm.active');
        if (activeLvlPill) lvl = (activeLvlPill.getAttribute('data-value') || '').trim().toLowerCase();
      } else if (levelSelect) {
        lvl = levelSelect.value.trim().toLowerCase();
      }

      var prc = priceSelect ? priceSelect.value : '';
      var sort = sortSelect ? sortSelect.value : 'relevance';

      var filtered = allCourses.filter(function (c) {
        // Query match
        if (q) {
          var title = (c.title || '').toLowerCase();
          var sub = (c.subtitle || c.description || '').toLowerCase();
          if (title.indexOf(q) === -1 && sub.indexOf(q) === -1) return false;
        }

        // Category match
        if (cat) {
          var cCat = (c.category_name || c.category || '').toLowerCase();
          var cSlug = (c.category_slug || '').toLowerCase() || cCat.replace(/[^a-z0-9]+/g, '-');
          var catSlug = cat.replace(/[^a-z0-9]+/g, '-');
          if (cCat !== cat && cCat.indexOf(cat) === -1 && cSlug !== catSlug) return false;
        }

        // Level match
        if (lvl) {
          var cLvl = (c.level || '').toLowerCase();
          if (cLvl !== lvl) return false;
        }

        // Price match
        if (prc) {
          var p = Number(c.price || 0);
          if (prc === 'under-2k' && (p <= 0 || p >= 2000)) return false;
          if (prc === '2k-5k' && (p < 2000 || p > 5000)) return false;
          if (prc === '5k-10k' && (p < 5000 || p > 10000)) return false;
          if (prc === 'above-10k' && p <= 10000) return false;
        }

        return true;
      });

      // Sorting
      if (sort === 'price-low') {
        filtered.sort(function (a, b) { return Number(a.price || 0) - Number(b.price || 0); });
      } else if (sort === 'price-high') {
        filtered.sort(function (a, b) { return Number(b.price || 0) - Number(a.price || 0); });
      } else if (sort === 'newest') {
        filtered.sort(function (a, b) { return (b.id || 0) - (a.id || 0); });
      }

      if (countEl) {
        countEl.textContent = filtered.length + ' course' + (filtered.length === 1 ? '' : 's') + ' available';
      }

      if (!filtered.length) {
        grid.innerHTML = (
          '<div style="grid-column: 1 / -1; padding: 60px 20px; text-align: center; border: 1px solid var(--tcm-border); background: var(--tcm-surface);">' +
            '<p class="tcm-h3" style="margin-bottom: 8px;">No matching courses found</p>' +
            '<p class="tcm-body-muted" style="margin-bottom: 24px;">Try clearing filters or searching for another tech topic.</p>' +
            '<button type="button" class="tcm-btn tcm-btn-secondary" id="resetCatalogFilters">Reset All Filters</button>' +
          '</div>'
        );
        var resetBtn = document.getElementById('resetCatalogFilters');
        if (resetBtn) {
          resetBtn.addEventListener('click', resetCatalogFilters);
        }
      } else {
        grid.innerHTML = filtered.map(renderCourseCard).join('');
      }

      syncUrlParams();
    }

    // Fetch courses from API
    getJSON(apiUrl('/api/courses'))
      .then(function (res) {
        if (!res || !res.success || !Array.isArray(res.data)) {
          grid.innerHTML = '<p class="tcm-body-muted">Failed to load courses. Please refresh.</p>';
          return;
        }
        allCourses = res.data;

        // Populate category dropdown options dynamically if needed
        if (catSelect && catSelect.options.length <= 1) {
          var seenCats = {};
          allCourses.forEach(function (c) {
            var cName = c.category_name || c.category;
            if (cName && !seenCats[cName]) {
              seenCats[cName] = true;
              var opt = document.createElement('option');
              opt.value = cName;
              opt.textContent = cName;
              catSelect.appendChild(opt);
            }
          });
        }

        if (params.get('cat')) selectCategoryValue(params.get('cat'));
        if (params.get('level')) selectLevelValue(params.get('level'));

        applyFilters();
      })
      .catch(function () {
        grid.innerHTML = '<p class="tcm-body-muted">Could not connect to course API.</p>';
      });

    // Wire filter listeners
    if (searchInput) {
      searchInput.addEventListener('input', function () { applyFilters(); });
    }
    if (catSelect) catSelect.addEventListener('change', function () {
      selectCategoryValue(catSelect.value);
      applyFilters();
    });
    if (levelSelect) levelSelect.addEventListener('change', function () {
      selectLevelValue(levelSelect.value);
      applyFilters();
    });
    if (priceSelect) priceSelect.addEventListener('change', applyFilters);
    if (sortSelect) sortSelect.addEventListener('change', applyFilters);

    if (catPillsWrap) {
      catPillsWrap.addEventListener('click', function (e) {
        var btn = e.target.closest('.tcm-pill-btn');
        if (!btn) return;
        var pills = catPillsWrap.querySelectorAll('.tcm-pill-btn');
        pills.forEach(function (p) { p.classList.remove('active'); });
        btn.classList.add('active');
        var val = btn.getAttribute('data-value') || '';
        if (catSelect) catSelect.value = val;
        applyFilters();
      });
    }

    if (levelPillsWrap) {
      levelPillsWrap.addEventListener('click', function (e) {
        var btn = e.target.closest('.tcm-pill-btn-sm');
        if (!btn) return;
        var pills = levelPillsWrap.querySelectorAll('.tcm-pill-btn-sm');
        pills.forEach(function (p) { p.classList.remove('active'); });
        btn.classList.add('active');
        var val = btn.getAttribute('data-value') || '';
        if (levelSelect) levelSelect.value = val;
        applyFilters();
      });
    }
  }

  /* ==========================================================================
     PAYU PAYMENT INTEGRATION HELPERS
     ========================================================================== */
  function submitPayUForm(action, params) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    form.style.display = 'none';

    for (var k in params) {
      if (Object.prototype.hasOwnProperty.call(params, k)) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = k;
        input.value = params[k];
        form.appendChild(input);
      }
    }

    document.body.appendChild(form);
    form.submit();
  }

  /* ==========================================================================
     WORKSHOP RESERVATION MODAL (PayU Integration)
     ========================================================================== */
  function wireWorkshopModal() {
    var overlay = document.getElementById('tcmWsModalOverlay');
    if (!overlay) return;

    var closeBtn = document.getElementById('tcmWsCloseBtn');
    var form = document.getElementById('tcmWsForm');
    var titleEl = document.getElementById('tcmWsTitle');
    var priceEl = document.getElementById('tcmWsPrice');
    var slugInput = document.getElementById('tcmWsSlug');
    var nameInput = document.getElementById('tcmWsName');
    var emailInput = document.getElementById('tcmWsEmail');
    var phoneInput = document.getElementById('tcmWsPhone');
    var feedback = document.getElementById('tcmWsFeedback');
    var submitBtn = document.getElementById('tcmWsSubmitBtn');

    // Extract UTM attributes from URL
    var urlParams = new URLSearchParams(window.location.search);
    var utmSource = urlParams.get('utm_source') || '';
    var utmMedium = urlParams.get('utm_medium') || '';
    var utmCampaign = urlParams.get('utm_campaign') || '';

    function openModal(slug, title, price) {
      if (slugInput) slugInput.value = slug || '';
      if (titleEl) titleEl.textContent = title || 'Live Cohort Workshop';
      if (priceEl) priceEl.textContent = 'Cohort Fee: ₹' + (price || '99') + ' (All Taxes Included)';
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span>Book My Seat &middot; ₹' + (price || '99') + '</span> <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>';
      }

      if (feedback) {
        feedback.style.display = 'none';
        feedback.innerHTML = '';
      }

      // Prefill stored info if available
      try {
        if (nameInput && !nameInput.value) nameInput.value = localStorage.getItem('tcm_lead_name') || '';
        if (emailInput && !emailInput.value) emailInput.value = localStorage.getItem('tcm_lead_email') || '';
        if (phoneInput && !phoneInput.value) phoneInput.value = localStorage.getItem('tcm_lead_phone') || '';
      } catch (e) {}

      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';

      setTimeout(function () {
        var first = (nameInput && !nameInput.value) ? nameInput : phoneInput;
        if (first) first.focus();
      }, 100);
    }

    function closeModal() {
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeModal();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && overlay.classList.contains('active')) closeModal();
    });

    // Delegate clicks on .tcm-trigger-workshop
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.tcm-trigger-workshop');
      if (!btn) return;
      e.preventDefault();

      var slug = btn.getAttribute('data-slug') || '';
      var title = btn.getAttribute('data-title') || '';
      var price = btn.getAttribute('data-price') || '99';

      openModal(slug, title, price);
      trackEvent('workshop_registration_started', {
        workshop_slug: slug,
        price: Number(price || 0)
      });
    });

    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var name = (nameInput ? nameInput.value : '').trim();
        var email = (emailInput ? emailInput.value : '').trim();
        var phone = (phoneInput ? phoneInput.value : '').trim();
        var slug = (slugInput ? slugInput.value : '').trim();

        if (!name) {
          showFeedback('Please enter your full name.', false);
          if (nameInput) nameInput.focus();
          return;
        }
        if (!email || email.indexOf('@') === -1) {
          showFeedback('Please enter a valid email address.', false);
          if (emailInput) emailInput.focus();
          return;
        }
        var cleanPhone = phone.replace(/[^\d]/g, '');
        if (cleanPhone.length < 10) {
          showFeedback('Please enter a valid 10-digit mobile number.', false);
          if (phoneInput) phoneInput.focus();
          return;
        }

        try {
          localStorage.setItem('tcm_lead_name', name);
          localStorage.setItem('tcm_lead_email', email);
          localStorage.setItem('tcm_lead_phone', phone);
        } catch (err) {}

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = 'Connecting to PayU...';
        }

        postJSON(apiUrl('/payment/initiate'), {
          item_type: 'workshop',
          slug: slug,
          name: name,
          email: email,
          phone: phone,
          utm_source: utmSource,
          utm_medium: utmMedium,
          utm_campaign: utmCampaign,
          landing_page: window.location.pathname
        }).then(function (res) {
          if (res && res.success && res.data && res.data.free && res.data.redirect) {
            trackEvent('workshop_registration_completed', { workshop_slug: slug, order: res.data.order_number });
            showFeedback(res.message || 'Seat reserved successfully! Redirecting...', true);
            if (submitBtn) {
              submitBtn.textContent = 'Seat Reserved!';
            }
            setTimeout(function () {
              window.location.href = res.data.redirect;
            }, 600);
            return;
          }
          if (res && res.success && res.data && res.data.action && res.data.params) {
            trackEvent('workshop_checkout_initiated', { workshop_slug: slug, order: res.data.order_number });
            showFeedback('Redirecting to PayU secure payment...', true);
            if (submitBtn) {
              submitBtn.textContent = 'Redirecting to PayU...';
            }
            setTimeout(function () {
              submitPayUForm(res.data.action, res.data.params);
            }, 300);
          } else {
            showFeedback((res && res.message) ? res.message : 'Unable to initiate payment.', false);
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = 'Reserve My Seat';
            }
          }
        }).catch(function () {
          showFeedback('Network error. Please try again or reach out on WhatsApp.', false);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Reserve My Seat';
          }
        });
      });
    }

    function showFeedback(msg, ok) {
      if (!feedback) return;
      feedback.style.display = 'block';
      feedback.style.padding = '12px 16px';
      feedback.style.marginBottom = '16px';
      feedback.style.border = '1px solid ' + (ok ? '#111111' : '#E23');
      feedback.style.background = ok ? '#FFFFFF' : '#FFF0F0';
      feedback.style.color = ok ? '#111111' : '#E23';
      feedback.style.fontSize = '13px';
      feedback.textContent = msg;
    }
  }

  /* ==========================================================================
     COURSE ENROLLMENT MODAL (PayU Integration)
     ========================================================================== */
  function wireCourseEnrollModal() {
    var overlay = document.getElementById('tcmCourseModalOverlay');
    if (!overlay) return;

    var closeBtn = document.getElementById('tcmCourseCloseBtn');
    var form = document.getElementById('tcmCourseForm');
    var titleEl = document.getElementById('tcmCourseModalTitle');
    var priceEl = document.getElementById('tcmCoursePrice');
    var slugInput = document.getElementById('tcmCourseSlug');
    var nameInput = document.getElementById('tcmCourseName');
    var emailInput = document.getElementById('tcmCourseEmail');
    var phoneInput = document.getElementById('tcmCoursePhone');
    var feedback = document.getElementById('tcmCourseFeedback');
    var submitBtn = document.getElementById('tcmCourseSubmitBtn');

    function openModal(slug, title, price) {
      if (slugInput) slugInput.value = slug || '';
      if (titleEl) titleEl.textContent = title ? ('Enroll in ' + title) : 'Course Enrollment';
      if (priceEl && price) priceEl.textContent = 'Tuition Fee: ₹' + Number(price).toLocaleString('en-IN') + ' (All Taxes Included)';
      if (submitBtn) {
        submitBtn.disabled = false;
        var pText = price ? (' · ₹' + Number(price).toLocaleString('en-IN')) : '';
        submitBtn.innerHTML = '<span>Proceed to Pay' + pText + '</span> <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>';
      }

      if (feedback) {
        feedback.style.display = 'none';
        feedback.innerHTML = '';
      }

      try {
        if (nameInput && !nameInput.value) nameInput.value = localStorage.getItem('tcm_lead_name') || '';
        if (emailInput && !emailInput.value) emailInput.value = localStorage.getItem('tcm_lead_email') || '';
        if (phoneInput && !phoneInput.value) phoneInput.value = localStorage.getItem('tcm_lead_phone') || '';
      } catch (e) {}

      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';

      setTimeout(function () {
        var first = (nameInput && !nameInput.value) ? nameInput : phoneInput;
        if (first) first.focus();
      }, 100);
    }

    function closeModal() {
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeModal();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && overlay.classList.contains('active')) closeModal();
    });

    // Delegate clicks on .tcm-trigger-course-enroll
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.tcm-trigger-course-enroll');
      if (!btn) return;
      e.preventDefault();

      var slug = btn.getAttribute('data-slug') || '';
      var title = btn.getAttribute('data-title') || '';
      var price = btn.getAttribute('data-price') || '0';

      openModal(slug, title, price);
      trackEvent('course_checkout_started', {
        course_slug: slug,
        price: Number(price || 0)
      });
    });

    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var name = (nameInput ? nameInput.value : '').trim();
        var email = (emailInput ? emailInput.value : '').trim();
        var phone = (phoneInput ? phoneInput.value : '').trim();
        var slug = (slugInput ? slugInput.value : '').trim();

        if (!name) {
          showFeedback('Please enter your full name.', false);
          if (nameInput) nameInput.focus();
          return;
        }
        if (!email || email.indexOf('@') === -1) {
          showFeedback('Please enter a valid email address.', false);
          if (emailInput) emailInput.focus();
          return;
        }
        var cleanPhone = phone.replace(/[^\d]/g, '');
        if (cleanPhone.length < 10) {
          showFeedback('Please enter a valid 10-digit mobile number.', false);
          if (phoneInput) phoneInput.focus();
          return;
        }

        try {
          localStorage.setItem('tcm_lead_name', name);
          localStorage.setItem('tcm_lead_email', email);
          localStorage.setItem('tcm_lead_phone', phone);
        } catch (err) {}

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = 'Connecting to PayU...';
        }

        postJSON(apiUrl('/payment/initiate'), {
          item_type: 'course',
          slug: slug,
          name: name,
          email: email,
          phone: phone
        }).then(function (res) {
          if (res && res.success && res.data && res.data.action && res.data.params) {
            trackEvent('course_checkout_initiated', { course_slug: slug, order: res.data.order_number });
            showFeedback('Redirecting to PayU secure payment...', true);
            if (submitBtn) {
              submitBtn.textContent = 'Redirecting to PayU...';
            }
            setTimeout(function () {
              submitPayUForm(res.data.action, res.data.params);
            }, 300);
          } else {
            showFeedback((res && res.message) ? res.message : 'Unable to initiate payment.', false);
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = 'Proceed to Pay';
            }
          }
        }).catch(function () {
          showFeedback('Network error. Please try again or reach out on WhatsApp.', false);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Proceed to Pay';
          }
        });
      });
    }

    function showFeedback(msg, ok) {
      if (!feedback) return;
      feedback.style.display = 'block';
      feedback.style.padding = '12px 16px';
      feedback.style.marginBottom = '16px';
      feedback.style.border = '1px solid ' + (ok ? '#111111' : '#E23');
      feedback.style.background = ok ? '#FFFFFF' : '#FFF0F0';
      feedback.style.color = ok ? '#111111' : '#E23';
      feedback.style.fontSize = '13px';
      feedback.textContent = msg;
    }
  }

  /* ==========================================================================
     BROCHURE LEAD MODAL
     ========================================================================== */
  function wireBrochureModal() {
    var overlay = document.getElementById('tcmBrochureOverlay');
    if (!overlay) return;

    var closeBtn = document.getElementById('tcmBrochureCloseBtn');
    var form = document.getElementById('tcmBrochureForm');
    var slugInput = document.getElementById('tcmBrochureSlug');
    var titleEl = document.getElementById('tcmBrochureTitle');
    var nameInput = document.getElementById('tcmBrochureName');
    var emailInput = document.getElementById('tcmBrochureEmail');
    var phoneInput = document.getElementById('tcmBrochurePhone');
    var feedback = document.getElementById('tcmBrochureFeedback');
    var submitBtn = document.getElementById('tcmBrochureSubmitBtn');

    function openModal(slug, title) {
      if (slugInput) slugInput.value = slug || '';
      if (titleEl) titleEl.textContent = title ? (title + ' — Official Brochure') : 'Download Official Course Brochure';
      if (feedback) feedback.style.display = 'none';

      try {
        if (nameInput && !nameInput.value) nameInput.value = localStorage.getItem('tcm_lead_name') || '';
        if (emailInput && !emailInput.value) emailInput.value = localStorage.getItem('tcm_lead_email') || '';
        if (phoneInput && !phoneInput.value) phoneInput.value = localStorage.getItem('tcm_lead_phone') || '';
      } catch (e) {}

      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      setTimeout(function () {
        var first = (nameInput && !nameInput.value) ? nameInput : phoneInput;
        if (first) first.focus();
      }, 100);
    }

    function closeModal() {
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeModal();
    });

    document.addEventListener('click', function (e) {
      var trigger = e.target.closest('.tcm-trigger-brochure');
      if (!trigger) return;
      e.preventDefault();

      var slug = trigger.getAttribute('data-slug') || '';
      var title = trigger.getAttribute('data-title') || '';
      openModal(slug, title);
    });

    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var name = (nameInput ? nameInput.value : '').trim();
        var email = (emailInput ? emailInput.value : '').trim();
        var phone = (phoneInput ? phoneInput.value : '').trim();
        var slug = (slugInput ? slugInput.value : '').trim();

        if (!name || !email || phone.replace(/[^\d]/g, '').length < 10) {
          if (feedback) {
            feedback.style.display = 'block';
            feedback.style.padding = '10px 14px';
            feedback.style.marginBottom = '14px';
            feedback.style.border = '1px solid #E23';
            feedback.style.background = '#FFF0F0';
            feedback.style.color = '#E23';
            feedback.style.fontSize = '13px';
            feedback.textContent = 'Please fill all required fields correctly.';
          }
          return;
        }

        try {
          localStorage.setItem('tcm_lead_name', name);
          localStorage.setItem('tcm_lead_email', email);
          localStorage.setItem('tcm_lead_phone', phone);
        } catch (err) {}

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = 'Preparing PDF Download...';
        }

        postJSON(apiUrl('/api/courses/brochure-lead'), {
          name: name,
          email: email,
          phone: phone,
          course_slug: slug
        }).then(function (res) {
          if (res && res.success && res.data && res.data.download_url) {
            trackEvent('course_enquiry', { course_slug: slug, source: 'brochure_download' });
            if (feedback) {
              feedback.style.display = 'block';
              feedback.style.padding = '10px 14px';
              feedback.style.marginBottom = '14px';
              feedback.style.border = '1px solid #111';
              feedback.style.background = '#FFF';
              feedback.style.color = '#111';
              feedback.style.fontSize = '13px';
              feedback.innerHTML = 'Success! Starting your brochure download now...';
            }

            var dl = document.createElement('a');
            dl.href = res.data.download_url;
            dl.setAttribute('download', (slug || 'TCM-Course') + '-Brochure.pdf');
            dl.target = '_blank';
            document.body.appendChild(dl);
            dl.click();

            setTimeout(function () {
              if (dl.parentNode) dl.parentNode.removeChild(dl);
              closeModal();
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Download Brochure PDF';
              }
            }, 1800);
          } else {
            if (feedback) {
              feedback.style.display = 'block';
              feedback.style.padding = '10px 14px';
              feedback.style.marginBottom = '14px';
              feedback.style.border = '1px solid #E23';
              feedback.style.background = '#FFF0F0';
              feedback.style.color = '#E23';
              feedback.style.fontSize = '13px';
              feedback.textContent = (res && res.message) ? res.message : 'Unable to start download.';
            }
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = 'Download Brochure PDF';
            }
          }
        }).catch(function () {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Download Brochure PDF';
          }
        });
      });
    }
  }

  /* ==========================================================================
     CONTACT FORM (PAGE 6)
     ========================================================================== */
  function wireContactForm() {
    var form = document.getElementById('tcmContactForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var name = ((document.getElementById('ctName') || {}).value || '').trim();
      var email = ((document.getElementById('ctEmail') || {}).value || '').trim();
      var phone = ((document.getElementById('ctPhone') || {}).value || '').trim();
      var subject = ((document.getElementById('ctSubject') || {}).value || '').trim();
      var message = ((document.getElementById('ctMessage') || {}).value || '').trim();
      var notice = document.getElementById('ctNotice');
      var submitBtn = document.getElementById('ctSubmitBtn');

      function showNotice(msg, ok) {
        if (!notice) return;
        notice.style.display = 'block';
        notice.style.padding = '14px 18px';
        notice.style.marginBottom = '20px';
        notice.style.border = '1px solid ' + (ok ? '#111111' : '#E23');
        notice.style.background = ok ? '#FFFFFF' : '#FFF0F0';
        notice.style.color = ok ? '#111111' : '#E23';
        notice.style.fontSize = '14px';
        notice.textContent = msg;
      }

      if (!name) return showNotice('Please enter your name.', false);
      if (!email || !document.getElementById('ctEmail').validity.valid) return showNotice('Please enter a valid email address.', false);
      if (!message || message.length < 5) return showNotice('Please write a message of at least 5 characters.', false);

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending Message...';
      }

      postJSON(apiUrl('/api/contact'), {
        name: name,
        email: email,
        phone: phone,
        subject: subject,
        message: message
      }).then(function (res) {
        if (res && res.success) {
          trackEvent('contact_lead_created', { subject: subject });
          showNotice(res.message || 'Thank you! Your message has been sent. We reply within 24 hours.', true);
          form.reset();
        } else {
          showNotice((res && res.message) ? res.message : 'Unable to send message.', false);
        }
      }).catch(function () {
        showNotice('Network error. Please try again.', false);
      }).finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send Message';
        }
      });
    });
  }

  /* ==========================================================================
     INSTITUTIONAL PARTNERSHIP FORM (PAGE 5 — /for-institutions)
     ========================================================================== */
  function wireInstitutionForm() {
    var form = document.getElementById('tcmInstitutionForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var instName = ((document.getElementById('instName') || {}).value || '').trim();
      var contactPerson = ((document.getElementById('instPerson') || {}).value || '').trim();
      var email = ((document.getElementById('instEmail') || {}).value || '').trim();
      var phone = ((document.getElementById('instPhone') || {}).value || '').trim();
      var city = ((document.getElementById('instCity') || {}).value || '').trim();
      var studentCount = ((document.getElementById('instStudents') || {}).value || '').trim();
      var model = ((document.getElementById('instModel') || {}).value || 'Academic Partnership').trim();
      var message = ((document.getElementById('instMessage') || {}).value || '').trim();
      var notice = document.getElementById('instNotice');
      var submitBtn = document.getElementById('instSubmitBtn');

      function showNotice(msg, ok) {
        if (!notice) return;
        notice.style.display = 'block';
        notice.style.padding = '14px 18px';
        notice.style.marginBottom = '20px';
        notice.style.border = '1px solid ' + (ok ? '#111111' : '#E23');
        notice.style.background = ok ? '#FFFFFF' : '#FFF0F0';
        notice.style.color = ok ? '#111111' : '#E23';
        notice.style.fontSize = '14px';
        notice.textContent = msg;
      }

      if (!instName) return showNotice('Please enter the institution or college name.', false);
      if (!contactPerson) return showNotice('Please enter the contact person name.', false);
      if (!email || email.indexOf('@') === -1) return showNotice('Please enter a valid official email address.', false);
      if (phone.replace(/[^\d]/g, '').length < 8) return showNotice('Please enter a valid phone number.', false);
      if (!city) return showNotice('Please enter your city.', false);

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting Application...';
      }

      postJSON(apiUrl('/api/partner/apply'), {
        institution_name: instName,
        contact_person: contactPerson,
        email: email,
        phone: phone,
        city: city,
        student_count: studentCount,
        partnership_model: model,
        message: message
      }).then(function (res) {
        if (res && res.success) {
          trackEvent('institution_application_submitted', { institution: instName });
          var msg = res.message || 'Thank you! Your institutional partnership inquiry has been received.';
          if (res.data && res.data.ref_code) {
            msg += ' (Reference ID: ' + res.data.ref_code + ')';
          }
          showNotice(msg, true);
          form.reset();
        } else {
          showNotice((res && res.message) ? res.message : 'Unable to submit application.', false);
        }
      }).catch(function () {
        showNotice('Network error. Please try again.', false);
      }).finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit Partnership Enquiry';
        }
      });
    });
  }

  /* ==========================================================================
     INSTITUTIONAL SMART REVENUE CALCULATOR
     ========================================================================== */
  function wireInstitutionCalculator() {
    var card = document.getElementById('instRevenueCalc');
    if (!card) return;

    var btnAcademic = document.getElementById('calcModelAcademic');
    var btnReferral = document.getElementById('calcModelReferral');
    var sliderStudents = document.getElementById('calcStudentSlider');
    var sliderFee = document.getElementById('calcFeeSlider');
    var sliderBatches = document.getElementById('calcBatchesSlider');
    var sliderPct = document.getElementById('calcPctSlider');
    var pctBtns = card.querySelectorAll('.ptr-pct-btn');

    var valStudents = document.getElementById('calcStudentVal');
    var valFee = document.getElementById('calcFeeVal');
    var valBatches = document.getElementById('calcBatchesVal');
    var valPct = document.getElementById('calcPctVal');

    var outInstitute = document.getElementById('calcOutInstitute');
    var outBatchesCombined = document.getElementById('calcOutBatchesCombined');
    var outTotal = document.getElementById('calcOutTotal');
    var outTcm = document.getElementById('calcOutTcm');
    var outPerBatch = document.getElementById('calcOutPerBatch');
    var outModelLabel = document.getElementById('calcOutModelLabel');

    var currentModel = 'Academic';
    var currentPct = 40;

    function updateCalc() {
      var students = parseInt(sliderStudents ? sliderStudents.value : 40, 10) || 40;
      var fee = parseInt(sliderFee ? sliderFee.value : 5000, 10) || 5000;
      var batches = parseInt(sliderBatches ? sliderBatches.value : 2, 10) || 2;
      var pct = currentPct;

      if (valStudents) valStudents.textContent = students + ' Students';
      if (valFee) valFee.textContent = '₹' + fee.toLocaleString('en-IN');
      if (valBatches) valBatches.textContent = batches + (batches === 1 ? ' Batch' : ' Batches');
      if (valPct) valPct.textContent = pct + '%';

      var totalCollections = students * fee * batches;
      var instituteShare = Math.round(totalCollections * (pct / 100));
      var tcmShare = totalCollections - instituteShare;
      var perBatch = Math.round(instituteShare / batches);

      if (outInstitute) outInstitute.textContent = '₹' + instituteShare.toLocaleString('en-IN');
      if (outBatchesCombined) outBatchesCombined.textContent = '₹' + instituteShare.toLocaleString('en-IN') + ' across ' + batches + (batches === 1 ? ' batch' : ' batches');
      if (outTotal) outTotal.textContent = '₹' + totalCollections.toLocaleString('en-IN');
      if (outTcm) outTcm.textContent = '₹' + tcmShare.toLocaleString('en-IN');
      if (outPerBatch) outPerBatch.textContent = '₹' + perBatch.toLocaleString('en-IN');
      if (outModelLabel) {
        outModelLabel.textContent = currentModel === 'Academic' ? 'Est. Institute Revenue Share (40%)' : 'Est. Referral Commission (15%)';
      }
    }

    function setModel(model) {
      currentModel = model;
      if (model === 'Academic') {
        currentPct = 40;
        if (btnAcademic) btnAcademic.classList.add('active');
        if (btnReferral) btnReferral.classList.remove('active');
        if (sliderPct) sliderPct.value = 40;
      } else {
        currentPct = 15;
        if (btnReferral) btnReferral.classList.add('active');
        if (btnAcademic) btnAcademic.classList.remove('active');
        if (sliderPct) sliderPct.value = 15;
      }
      pctBtns.forEach(function (btn) {
        var p = parseInt(btn.getAttribute('data-pct'), 10);
        btn.classList.toggle('active', p === currentPct);
      });

      // Sync form dropdown if present
      var instModelSelect = document.getElementById('instModel');
      if (instModelSelect) {
        instModelSelect.value = model === 'Academic' ? 'Academic Partnership Model' : 'Referral Model';
      }

      updateCalc();
    }

    if (btnAcademic) btnAcademic.addEventListener('click', function () { setModel('Academic'); });
    if (btnReferral) btnReferral.addEventListener('click', function () { setModel('Referral'); });

    if (sliderStudents) sliderStudents.addEventListener('input', updateCalc);
    if (sliderFee) sliderFee.addEventListener('input', updateCalc);
    if (sliderBatches) sliderBatches.addEventListener('input', updateCalc);
    if (sliderPct) sliderPct.addEventListener('input', function () {
      currentPct = parseInt(sliderPct.value, 10) || 40;
      updateCalc();
    });

    pctBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var p = parseInt(btn.getAttribute('data-pct'), 10) || 40;
        currentPct = p;
        if (p === 15) setModel('Referral');
        else setModel('Academic');
      });
    });

    // Handle external trigger buttons with data-select-partner-model
    document.querySelectorAll('[data-select-partner-model]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var m = btn.getAttribute('data-select-partner-model');
        setModel(m === 'Referral' ? 'Referral' : 'Academic');
        var calcSec = document.getElementById('calculator');
        if (calcSec) calcSec.scrollIntoView({ behavior: 'smooth' });
      });
    });

    updateCalc();
  }

  /* ==========================================================================
     CERTIFICATE ZOOM MODAL
     ========================================================================== */
  function wireCertificateModal() {
    var modal = document.getElementById('certZoomModal');
    var closeBtn = document.getElementById('certZoomClose');
    var imgEl = document.getElementById('certZoomImg');
    var titleEl = document.getElementById('certZoomTitle');
    var cards = document.querySelectorAll('.tcm-cert-card, [data-preview-src]');

    if (!modal || !cards.length) return;
    var activeCard = null;
    var previousOverflow = '';

    function openCert(src, title) {
      previousOverflow = document.body.style.overflow;
      if (imgEl) imgEl.src = src;
      if (titleEl) titleEl.textContent = title || 'Preview';
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      if (closeBtn) closeBtn.focus();
    }

    function closeCert() {
      modal.style.display = 'none';
      if (imgEl) imgEl.src = '';
      document.body.style.overflow = previousOverflow;
      if (activeCard) activeCard.focus();
    }

    cards.forEach(function (card) {
      card.addEventListener('click', function () {
        activeCard = card;
        var src = card.getAttribute('data-preview-src') || card.getAttribute('data-cert-src');
        var title = card.getAttribute('data-preview-title') || card.getAttribute('data-cert-title');
        if (src) openCert(src, title);
      });
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', closeCert);
    }

    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeCert();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.style.display === 'flex') {
        closeCert();
      }
    });
  }

  /* ==========================================================================
     HERO WORKSHOP SWIPEABLE STACKED DECK
     ========================================================================== */
  function wireHeroWorkshopDeck() {
    var deckWrapper = document.getElementById('heroWorkshopDeck');
    if (!deckWrapper) return;

    var stack = document.getElementById('heroDeckStack');
    if (!stack) return;

    var cards = Array.prototype.slice.call(stack.querySelectorAll('.tcm-deck-card'));
    if (cards.length === 0) return;

    var counter = document.getElementById('heroDeckCounter');
    var prevBtn = document.getElementById('heroDeckPrevBtn');
    var nextBtn = document.getElementById('heroDeckNextBtn');
    var dotsContainer = document.getElementById('heroDeckDots');
    var dots = dotsContainer ? Array.prototype.slice.call(dotsContainer.querySelectorAll('.tcm-deck-dot')) : [];

    var activeIndex = 0;
    var total = cards.length;
    var isAnimating = false;
    var autoPlayTimer = null;

    function renderDeck(newActiveIndex, direction) {
      if (isAnimating) return;
      isAnimating = true;

      var prevActive = activeIndex;
      activeIndex = (newActiveIndex + total) % total;

      // Handle exit swipe animation
      if (direction) {
        var leavingCard = cards[prevActive];
        if (leavingCard) {
          leavingCard.classList.remove('is-swiping-left', 'is-swiping-right');
          leavingCard.classList.add(direction === 'left' ? 'is-swiping-left' : 'is-swiping-right');
        }
      }

      var delay = direction ? 260 : 0;

      setTimeout(function () {
        cards.forEach(function (card, idx) {
          card.classList.remove('is-swiping-left', 'is-swiping-right', 'is-active', 'is-next', 'is-next2', 'is-hidden', 'is-dragging');
          card.style.transform = '';
          card.style.opacity = '';

          var k = (idx - activeIndex + total) % total;

          if (k === 0) {
            card.classList.add('is-active');
            card.setAttribute('aria-hidden', 'false');
          } else if (k === 1) {
            card.classList.add('is-next');
            card.setAttribute('aria-hidden', 'true');
          } else if (k === 2) {
            card.classList.add('is-next2');
            card.setAttribute('aria-hidden', 'true');
          } else {
            card.classList.add('is-hidden');
            card.setAttribute('aria-hidden', 'true');
          }
        });

        // Update counter
        if (counter) {
          counter.textContent = (activeIndex + 1) + ' / ' + total;
        }

        // Update dots
        dots.forEach(function (dot, i) {
          if (i === activeIndex) {
            dot.classList.add('is-active');
            dot.setAttribute('aria-current', 'true');
          } else {
            dot.classList.remove('is-active');
            dot.removeAttribute('aria-current');
          }
        });

        isAnimating = false;
      }, delay);
    }

    // Pointer / touch gesture handling
    var startX = 0;
    var startY = 0;
    var currentX = 0;
    var currentY = 0;
    var isDragging = false;
    var hasDragged = false;
    var startTime = 0;
    var activeCard = null;

    function getCoords(e) {
      if (e.touches && e.touches.length > 0) {
        return { x: e.touches[0].clientX, y: e.touches[0].clientY };
      }
      return { x: e.clientX, y: e.clientY };
    }

    function onPointerStart(e) {
      if (isAnimating) return;
      var targetCard = e.target.closest('.tcm-deck-card');
      if (!targetCard) return;

      // If user tapped a peeked card behind, immediately bring it to front
      if (targetCard.classList.contains('is-next')) {
        e.preventDefault();
        renderDeck(activeIndex + 1, 'left');
        resetAutoPlay();
        return;
      }
      if (targetCard.classList.contains('is-next2')) {
        e.preventDefault();
        renderDeck(activeIndex + 2, 'left');
        resetAutoPlay();
        return;
      }

      if (!targetCard.classList.contains('is-active')) return;

      // Allow clicks on buttons or links inside the active card
      if (e.target.closest('button, a, input')) {
        return;
      }

      var coords = getCoords(e);
      startX = coords.x;
      startY = coords.y;
      currentX = startX;
      currentY = startY;
      isDragging = true;
      hasDragged = false;
      startTime = Date.now();
      activeCard = targetCard;

      activeCard.classList.add('is-dragging');
      pauseAutoPlay();
    }

    function onPointerMove(e) {
      if (!isDragging || !activeCard) return;
      var coords = getCoords(e);
      currentX = coords.x;
      currentY = coords.y;

      var deltaX = currentX - startX;
      var deltaY = currentY - startY;

      if (Math.abs(deltaX) > 8) {
        hasDragged = true;
      }

      var rot = deltaX * 0.04;
      activeCard.style.transform = 'translate3d(' + deltaX + 'px, ' + (deltaY * 0.15) + 'px, 0) rotate(' + rot + 'deg)';

      // Dynamically scale card behind
      var nextCard = cards[(activeIndex + 1) % total];
      if (nextCard) {
        var progress = Math.min(1, Math.abs(deltaX) / 200);
        var curScale = 0.95 + (0.05 * progress);
        var curY = -14 + (14 * progress);
        nextCard.style.transform = 'translate3d(0, ' + curY + 'px, 0) scale(' + curScale + ')';
      }
    }

    function onPointerEnd() {
      if (!isDragging) return;
      isDragging = false;

      if (activeCard) {
        activeCard.classList.remove('is-dragging');
      }

      var deltaX = currentX - startX;
      var duration = Date.now() - startTime;
      var velocity = Math.abs(deltaX) / (duration || 1);

      var nextCard = cards[(activeIndex + 1) % total];
      if (nextCard) {
        nextCard.style.transform = '';
      }

      // Check threshold (swipe distance > 55px or fast flick)
      if (Math.abs(deltaX) > 55 || velocity > 0.45) {
        if (deltaX < 0) {
          renderDeck(activeIndex + 1, 'left');
        } else {
          renderDeck(activeIndex - 1, 'right');
        }
      } else {
        // Snap back to front
        if (activeCard) {
          activeCard.style.transform = '';
        }
      }

      activeCard = null;
      resetAutoPlay();
    }

    // Attach drag events to stack
    stack.addEventListener('mousedown', onPointerStart);
    window.addEventListener('mousemove', onPointerMove);
    window.addEventListener('mouseup', onPointerEnd);

    stack.addEventListener('touchstart', onPointerStart, { passive: true });
    window.addEventListener('touchmove', onPointerMove, { passive: true });
    window.addEventListener('touchend', onPointerEnd);
    window.addEventListener('touchcancel', onPointerEnd);

    // Suppress button click if dragged
    stack.addEventListener('click', function (e) {
      if (hasDragged) {
        e.preventDefault();
        e.stopPropagation();
        hasDragged = false;
      }
    }, true);

    // Prev & Next Buttons
    if (prevBtn) {
      prevBtn.addEventListener('click', function (e) {
        e.preventDefault();
        renderDeck(activeIndex - 1, 'right');
        resetAutoPlay();
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', function (e) {
        e.preventDefault();
        renderDeck(activeIndex + 1, 'left');
        resetAutoPlay();
      });
    }

    // Dots navigation
    dots.forEach(function (dot) {
      dot.addEventListener('click', function (e) {
        e.preventDefault();
        var targetIdx = parseInt(dot.getAttribute('data-index'), 10);
        if (!isNaN(targetIdx) && targetIdx !== activeIndex) {
          var dir = targetIdx > activeIndex ? 'left' : 'right';
          renderDeck(targetIdx, dir);
          resetAutoPlay();
        }
      });
    });

    // Keyboard support (left/right arrows)
    deckWrapper.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') {
        renderDeck(activeIndex - 1, 'right');
        resetAutoPlay();
      } else if (e.key === 'ArrowRight') {
        renderDeck(activeIndex + 1, 'left');
        resetAutoPlay();
      }
    });

    // Auto-advance loop (every 5.5s)
    function startAutoPlay() {
      stopAutoPlay();
      autoPlayTimer = setInterval(function () {
        renderDeck(activeIndex + 1, 'left');
      }, 5500);
    }

    function stopAutoPlay() {
      if (autoPlayTimer) {
        clearInterval(autoPlayTimer);
        autoPlayTimer = null;
      }
    }

    function pauseAutoPlay() {
      stopAutoPlay();
    }

    function resetAutoPlay() {
      startAutoPlay();
    }

    deckWrapper.addEventListener('mouseenter', pauseAutoPlay);
    deckWrapper.addEventListener('mouseleave', resetAutoPlay);

    // Initialize deck
    renderDeck(0);
    startAutoPlay();
  }

  /* ==========================================================================
     PLACEMENT REPORT MODAL
     ========================================================================== */
  function wirePlacementReportModal() {
    var openBtn = document.getElementById('openPlacementReportBtn');
    var overlay = document.getElementById('tcmPlacementOverlay');
    var closeBtn = document.getElementById('tcmPlacementCloseBtn');
    var downloadBtn = document.getElementById('downloadPlacementPdfBtn');
    var feedback = document.getElementById('placementDownloadFeedback');

    if (!openBtn || !overlay) return;

    function openModal() {
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      overlay.classList.remove('active');
      document.body.style.overflow = '';
      if (feedback) feedback.style.display = 'none';
    }

    openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeModal();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && overlay.classList.contains('active')) {
        closeModal();
      }
    });

    if (downloadBtn) {
      downloadBtn.addEventListener('click', function () {
        if (feedback) {
          feedback.style.display = 'block';
        }
      });
    }
  }

  /* ==========================================================================
     LANGUAGE SELECTOR & GOOGLE TRANSLATE CLIENT
     ========================================================================== */
  function wireLanguageSelector() {
    var pickers = document.querySelectorAll('.tcm-lang-picker');
    if (!pickers.length) return;

    var langLabels = {
      en: 'EN',
      hi: 'HI',
      mr: 'MR',
      gu: 'GU',
      bn: 'BN',
      ta: 'TA',
      te: 'TE',
      kn: 'KN',
      ml: 'ML',
      pa: 'PA',
      ur: 'UR',
      ar: 'AR',
      fr: 'FR',
      es: 'ES',
      de: 'DE',
      it: 'IT',
      pt: 'PT',
      ru: 'RU',
      ko: 'KO',
      'zh-CN': 'ZH',
      ja: 'JA',
      ne: 'NE',
      th: 'TH',
      vi: 'VI'
    };

    var savedLang = 'en';
    try {
      savedLang = localStorage.getItem('tcm_site_lang') || 'en';
    } catch (e) {}

    if (savedLang === 'en') {
      var match = document.cookie.match(/(?:^|;\s*)googtrans=\/en\/([a-zA-Z\-]+)/);
      if (match && match[1] && match[1] !== 'en') {
        savedLang = match[1];
      }
    }

    function syncLangUI(lang) {
      var code = langLabels[lang] || lang.toUpperCase();
      document.querySelectorAll('.tcm-lang-code').forEach(function (el) {
        el.textContent = code;
      });
      document.querySelectorAll('.tcm-lang-option').forEach(function (opt) {
        var optLang = opt.getAttribute('data-lang');
        opt.classList.toggle('active', optLang === lang);
      });
    }

    function ensureTranslateScript(cb) {
      if (window.google && window.google.translate && window.google.translate.TranslateElement) {
        if (cb) cb();
        return;
      }
      if (!document.getElementById('google_translate_element')) {
        var el = document.createElement('div');
        el.id = 'google_translate_element';
        el.style.display = 'none';
        document.body.appendChild(el);
      }
      window.tcmGoogleTranslateInit = function () {
        try {
          new window.google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'en,hi,mr,gu,bn,ta,te,kn,ml,pa,ur,ar,fr,es,de,it,pt,ru,ko,zh-CN,ja,ne,th,vi',
            autoDisplay: false
          }, 'google_translate_element');
          if (cb) cb();
        } catch (err) {}
      };

      if (!document.getElementById('tcmGoogleTranslateScript')) {
        var s = document.createElement('script');
        s.id = 'tcmGoogleTranslateScript';
        s.src = 'https://translate.google.com/translate_a/element.js?cb=tcmGoogleTranslateInit';
        s.async = true;
        document.head.appendChild(s);
      }
    }

    function applyTranslation(lang, attempts) {
      attempts = attempts || 0;
      var target = lang === 'en' ? '/en/en' : '/en/' + lang;
      var expires = 'expires=Fri, 31 Dec 9999 23:59:59 GMT';
      document.cookie = 'googtrans=' + target + '; ' + expires + '; path=/';
      if (location.hostname.indexOf('.') !== -1) {
        document.cookie = 'googtrans=' + target + '; ' + expires + '; path=/; domain=.' + location.hostname.replace(/^www\./, '');
      }

      var combo = document.querySelector('.goog-te-combo');
      if (combo) {
        combo.value = lang === 'en' ? '' : lang;
        combo.dispatchEvent(new Event('change'));
        return;
      }

      ensureTranslateScript(function () {
        var c = document.querySelector('.goog-te-combo');
        if (c) {
          c.value = lang === 'en' ? '' : lang;
          c.dispatchEvent(new Event('change'));
        } else if (attempts < 10) {
          setTimeout(function () {
            applyTranslation(lang, attempts + 1);
          }, 300);
        }
      });
    }

    syncLangUI(savedLang);
    if (savedLang !== 'en') {
      ensureTranslateScript(function () {
        applyTranslation(savedLang);
      });
    }

    pickers.forEach(function (picker) {
      var btn = picker.querySelector('.tcm-lang-btn');
      if (!btn) return;
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = picker.classList.contains('open');
        document.querySelectorAll('.tcm-lang-picker.open').forEach(function (p) {
          p.classList.remove('open');
          var b = p.querySelector('.tcm-lang-btn');
          if (b) b.setAttribute('aria-expanded', 'false');
        });
        if (!isOpen) {
          picker.classList.add('open');
          btn.setAttribute('aria-expanded', 'true');
        }
      });

      picker.querySelectorAll('.tcm-lang-option').forEach(function (opt) {
        opt.addEventListener('click', function (e) {
          e.stopPropagation();
          var lang = opt.getAttribute('data-lang') || 'en';
          try {
            localStorage.setItem('tcm_site_lang', lang);
          } catch (err) {}

          syncLangUI(lang);
          picker.classList.remove('open');
          btn.setAttribute('aria-expanded', 'false');

          if (lang === 'en') {
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            if (location.hostname.indexOf('.') !== -1) {
              document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + location.hostname.replace(/^www\./, '');
            }
            var combo = document.querySelector('.goog-te-combo');
            if (combo) {
              combo.value = '';
              combo.dispatchEvent(new Event('change'));
            } else {
              window.location.reload();
            }
          } else {
            applyTranslation(lang);
          }
        });
      });
    });

    document.addEventListener('click', function (e) {
      if (e.target.closest('.tcm-lang-picker')) return;
      document.querySelectorAll('.tcm-lang-picker.open').forEach(function (p) {
        p.classList.remove('open');
        var b = p.querySelector('.tcm-lang-btn');
        if (b) b.setAttribute('aria-expanded', 'false');
      });
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        document.querySelectorAll('.tcm-lang-picker.open').forEach(function (p) {
          p.classList.remove('open');
          var b = p.querySelector('.tcm-lang-btn');
          if (b) b.setAttribute('aria-expanded', 'false');
        });
      }
    });
  }

  /* ==========================================================================
     BOOK A DEMO MODAL
     ========================================================================== */
  function wireDemoModal() {
    var overlay = document.getElementById('tcmDemoModalOverlay');
    var closeBtn = document.getElementById('tcmDemoCloseBtn');
    var form = document.getElementById('tcmDemoForm');
    var successState = document.getElementById('demoSuccessState');

    function openModal() {
      if (!overlay) return;
      overlay.style.display = 'flex';
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      if (form) form.style.display = 'block';
      if (successState) successState.style.display = 'none';
      var nameInput = document.getElementById('demoFullName');
      if (nameInput) setTimeout(function () { nameInput.focus(); }, 120);
    }

    function closeModal() {
      if (!overlay) return;
      overlay.style.display = 'none';
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }

    document.addEventListener('click', function (e) {
      var trigger = e.target && e.target.closest ? e.target.closest('.demo-trigger, #toolsBookDemoBtn, #openDemoModalBtn') : null;
      if (trigger) {
        e.preventDefault();
        openModal();
      }
    });

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (overlay) {
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
      });
    }

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && overlay && overlay.classList.contains('active')) {
        closeModal();
      }
    });

    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var name = (document.getElementById('demoFullName') || {}).value || '';
        var phone = (document.getElementById('demoPhone') || {}).value || '';
        var email = (document.getElementById('demoEmail') || {}).value || '';
        var track = (document.getElementById('demoTrack') || {}).value || '';
        var stage = (document.getElementById('demoStage') || {}).value || '';

        if (!name.trim() || !phone.trim() || !email.trim()) {
          alert('Please fill in your name, phone number, and email.');
          return;
        }

        postJSON(apiUrl('/api/contact'), {
          name: name,
          email: email,
          phone: phone,
          subject: 'Book a Demo Request: ' + track,
          message: 'Stage: ' + stage + ' | Track: ' + track
        }).catch(function () {});

        try {
          localStorage.setItem('tcm_lead_name', name);
          localStorage.setItem('tcm_lead_email', email);
          localStorage.setItem('tcm_lead_phone', phone);
        } catch (err) {}

        if (form) form.style.display = 'none';
        if (successState) successState.style.display = 'block';
      });
    }
  }

  function normalizeBrandLogos() {
    var logos = document.querySelectorAll('.tcm-brand-logo');
    var basePrefix = (BASE && BASE !== '/') ? BASE.replace(/\/+$/, '') : '';
    var absoluteLogoUrl = basePrefix + '/assets/logo.png';
    logos.forEach(function (img) {
      var src = img.getAttribute('src') || '';
      // Only normalize if empty or relative (./assets or assets/ without leading slash)
      if (!src || src === './assets/logo.png' || src === 'assets/logo.png' || src.startsWith('assets/')) {
        img.src = absoluteLogoUrl;
      } else if (basePrefix && src === '/assets/logo.png') {
        img.src = absoluteLogoUrl;
      }
      // Fail-safe: if logo fails to load for any reason, load directly from origin root
      img.onerror = function () {
        var rootLogo = (typeof window !== 'undefined' && window.location && window.location.origin ? window.location.origin : '') + '/assets/logo.png';
        if (img.src !== rootLogo) {
          img.src = rootLogo;
        }
      };
    });
  }

  /* ==========================================================================
     GLOBAL BOOT
     ========================================================================== */
  function boot() {
    normalizeSiteLinks();
    normalizeBrandLogos();
    wireNavigation();
    wireLanguageSelector();
    wireDemoModal();
    wireHeroWorkshopDeck();
    wirePlacementReportModal();
    hydrateHomeCategoryTracks();
    hydrateHomeCourses();
    initCourseCatalog();
    wireWorkshopModal();
    wireCourseEnrollModal();
    wireBrochureModal();
    wireContactForm();
    wireInstitutionForm();
    wireInstitutionCalculator();
    wireCertificateModal();

    // Check auth status
    getJSON(apiUrl('/api/me'))
      .then(function (res) {
        if (res && res.success && res.data) {
          window.TCM_USER = res.data;
          if (res.data.authenticated) {
            var authBtns = document.querySelectorAll('.tcm-auth-link');
            authBtns.forEach(function (btn) {
              btn.textContent = res.data.role === 'admin' ? 'Admin Panel' : 'My Dashboard';
              btn.setAttribute('href', apiUrl(res.data.dashboard_url || '/student'));
            });
          }
        }
      })
      .catch(function () {});
  }

  if (document.readyState !== 'loading') boot();
  else document.addEventListener('DOMContentLoaded', boot);
})();
