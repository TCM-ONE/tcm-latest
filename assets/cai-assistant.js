/**
 * Cai — TCM One Learning Advisor & Course Assistant
 * Clean, lightweight, isolated client module.
 * Queries actual course APIs without hallucination.
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
        var idx = src.indexOf('/assets/cai-assistant.js');
        if (idx !== -1) {
          try {
            var urlObj = new URL(src);
            var subPath = urlObj.pathname.substring(0, urlObj.pathname.indexOf('/assets/cai-assistant.js'));
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

  function apiUrl(p) {
    return BASE + p;
  }

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  var cachedCourses = null;
  var cachedWorkshops = null;

  function loadKnowledge() {
    if (!cachedCourses) {
      fetch(apiUrl('/api/courses'), { headers: { Accept: 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res && res.success && Array.isArray(res.data)) cachedCourses = res.data;
        })
        .catch(function () {});
    }
    if (!cachedWorkshops) {
      fetch(apiUrl('/api/workshops/featured'), { headers: { Accept: 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res && res.success && Array.isArray(res.data)) cachedWorkshops = res.data;
        })
        .catch(function () {});
    }
  }

  function initCai() {
    var l = document.getElementById('caiLauncher');
    if (l && l.parentNode) l.parentNode.removeChild(l);
    var w = document.getElementById('caiWindow');
    if (w && w.parentNode) w.parentNode.removeChild(w);
    return;
  }

    // Inject assistant window
    var win = document.createElement('div');
    win.id = 'caiWindow';
    win.className = 'cai-window';
    win.innerHTML = (
      '<div class="cai-header">' +
        '<div class="cai-title-wrap">' +
          '<h4><span class="cai-launcher-dot"></span> Cai &middot; TCM Advisor</h4>' +
          '<p>Real courses, workshops &amp; live guidance</p>' +
        '</div>' +
        '<button type="button" class="cai-close" id="caiCloseBtn" aria-label="Close Assistant">&times;</button>' +
      '</div>' +
      '<div class="cai-body" id="caiBody">' +
        '<div class="cai-msg cai-msg-assistant">' +
          '<p style="margin: 0 0 8px 0;">Hello! I am <strong>Cai</strong>, your TCM One learning advisor. How can I help you today?</p>' +
          '<div class="cai-prompt-pills">' +
            '<button type="button" class="cai-pill" data-intent="find-course">' +
              '<span>Find a course for my goals</span> &rarr;' +
            '</button>' +
            '<button type="button" class="cai-pill" data-intent="workshops">' +
              '<span>Tell me about live workshops (₹99 &amp; ₹999)</span> &rarr;' +
            '</button>' +
            '<button type="button" class="cai-pill" data-intent="fees">' +
              '<span>How do fees, batches &amp; EMI work?</span> &rarr;' +
            '</button>' +
            '<button type="button" class="cai-pill" data-intent="whatsapp">' +
              '<span>Connect with a mentor on WhatsApp</span> &rarr;' +
            '</button>' +
          '</div>' +
        '</div>' +
      '</div>' +
      '<div class="cai-footer">' +
        '<input type="text" id="caiInput" class="cai-input" placeholder="Ask about Python, MERN, AI...">' +
        '<button type="button" id="caiSend" class="cai-send">Send</button>' +
      '</div>'
    );
    document.body.appendChild(win);

    // Defer knowledge loading until user interacts or page is completely idle
    if ('requestIdleCallback' in window) {
      window.requestIdleCallback(function () { setTimeout(loadKnowledge, 1500); });
    } else {
      setTimeout(loadKnowledge, 2500);
    }

    function openWin() {
      loadKnowledge();
      win.classList.add('open');
      launcher.style.display = 'none';
      var input = document.getElementById('caiInput');
      if (input) input.focus();
    }

    function closeWin() {
      win.classList.remove('open');
      launcher.style.display = 'flex';
    }

    launcher.addEventListener('mouseenter', loadKnowledge, { once: true });
    launcher.addEventListener('click', openWin);
    var closeBtn = document.getElementById('caiCloseBtn');
    if (closeBtn) closeBtn.addEventListener('click', closeWin);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && win.classList.contains('open')) closeWin();
    });

    var body = document.getElementById('caiBody');
    var input = document.getElementById('caiInput');
    var send = document.getElementById('caiSend');

    function appendMsg(sender, html) {
      var d = document.createElement('div');
      d.className = 'cai-msg cai-msg-' + sender;
      d.innerHTML = html;
      body.appendChild(d);
      body.scrollTop = body.scrollHeight;
    }

    function handleIntent(intent) {
      if (intent === 'find-course') {
        appendMsg('user', 'Find a course for my goals');
        appendMsg('assistant', (
          '<p style="margin:0 0 8px;">What is your current background or learning objective?</p>' +
          '<div class="cai-prompt-pills">' +
            '<button type="button" class="cai-pill" data-topic="web">' +
              '<span>Full-Stack &amp; Web Development</span> &rarr;' +
            '</button>' +
            '<button type="button" class="cai-pill" data-topic="python">' +
              '<span>Python &amp; Programming Foundations</span> &rarr;' +
            '</button>' +
            '<button type="button" class="cai-pill" data-topic="ai">' +
              '<span>AI, Machine Learning &amp; Automation</span> &rarr;' +
            '</button>' +
            '<button type="button" class="cai-pill" data-topic="data">' +
              '<span>Data Analytics &amp; SQL</span> &rarr;' +
            '</button>' +
          '</div>'
        ));
      } else if (intent === 'workshops') {
        appendMsg('user', 'Tell me about live workshops');
        appendMsg('assistant', (
          '<p style="margin:0 0 8px;">TCM One conducts highly focused live online cohorts designed for hands-on skill building:</p>' +
          '<div class="cai-card">' +
            '<h5>Python Starter Workshop (3 Days) — ₹99</h5>' +
            '<p>Master Advanced Python, Web Scraping with BeautifulSoup, API Integration &amp; automated alert bots.</p>' +
            '<a href="' + apiUrl('/workshops/python-starter-workshop') + '" class="cai-card-btn">View Workshop Details &rarr;</a>' +
          '</div>' +
          '<div class="cai-card">' +
            '<h5>7-Day Frontend Website Challenge — ₹999</h5>' +
            '<p>Build and publish a fully responsive website with HTML, CSS, JavaScript and live mentor reviews.</p>' +
            '<a href="' + apiUrl('/workshops/frontend-website-challenge-7-days') + '" class="cai-card-btn">View Workshop Details &rarr;</a>' +
          '</div>'
        ));
      } else if (intent === 'fees') {
        appendMsg('user', 'How do fees, batches &amp; EMI work?');
        appendMsg('assistant', (
          '<p style="margin:0 0 8px;">All course fees at TCM One are transparently displayed without hidden charges:</p>' +
          '<ul style="margin:0 0 10px 18px; padding:0; font-size:12.5px; line-height:1.6;">' +
            '<li><strong>Live Mentored Cohorts:</strong> Fees include live sessions, code reviews, assignments, and verified certificates.</li>' +
            '<li><strong>Flexible Payments:</strong> EMI options are available for eligible programs priced above ₹5,000.</li>' +
            '<li><strong>Batch Schedule:</strong> Evening &amp; weekend live slots are available to accommodate college students and working professionals.</li>' +
          '</ul>' +
          '<a href="' + apiUrl('/courses') + '" class="cai-card-btn">Browse All Program Fees &rarr;</a>'
        ));
      } else if (intent === 'whatsapp') {
        appendMsg('user', 'Connect with a mentor on WhatsApp');
        var waUrl = 'https://wa.me/919238695500?text=' + encodeURIComponent('Hello TCM One! I would like guidance on choosing the right tech course.');
        appendMsg('assistant', (
          '<p style="margin:0 0 8px;">Our admissions mentors are available Monday to Saturday, 10 AM &mdash; 8 PM IST.</p>' +
          '<a href="' + waUrl + '" target="_blank" rel="noopener" class="tcm-btn tcm-btn-primary tcm-btn-sm" style="display:inline-flex;margin-top:4px;">' +
            '<span>Chat on WhatsApp (+91 9238695500)</span>' +
          '</a>'
        ));
      }
    }

    function searchCoursesByTopic(topic) {
      if (!cachedCourses) {
        appendMsg('assistant', 'Connecting to course catalog... please ask again in a moment.');
        return;
      }

      var matches = cachedCourses.filter(function (c) {
        var hay = ((c.title || '') + ' ' + (c.category_name || '') + ' ' + (c.slug || '')).toLowerCase();
        return hay.indexOf(topic.toLowerCase()) !== -1;
      }).slice(0, 3);

      if (!matches.length) {
        matches = cachedCourses.slice(0, 3);
      }

      var cardsHtml = matches.map(function (c) {
        return (
          '<div class="cai-card">' +
            '<h5>' + esc(c.title) + '</h5>' +
            '<p>' + esc(c.duration || 'Live Cohort') + ' &middot; ₹' + Number(c.price || 0).toLocaleString('en-IN') + '</p>' +
            '<a href="' + apiUrl('/courses/' + encodeURIComponent(c.slug)) + '" class="cai-card-btn">View Curriculum &rarr;</a>' +
          '</div>'
        );
      }).join('');

      appendMsg('assistant', (
        '<p style="margin:0 0 4px;">Here are recommended programs matching your interest:</p>' +
        cardsHtml +
        '<p style="margin:8px 0 0 0; font-size:12px; color:#666;">Need a personalized roadmap? ' +
          '<a href="' + apiUrl('/contact') + '" style="color:#111;text-decoration:underline;">Schedule a free career call</a>.</p>'
      ));
    }

    // Delegate clicks inside chat body
    body.addEventListener('click', function (e) {
      var pill = e.target.closest('.cai-pill');
      if (!pill) return;

      var intent = pill.getAttribute('data-intent');
      if (intent) {
        handleIntent(intent);
        return;
      }

      var topic = pill.getAttribute('data-topic');
      if (topic) {
        appendMsg('user', pill.querySelector('span') ? pill.querySelector('span').textContent : topic);
        searchCoursesByTopic(topic);
      }
    });

    function handleUserInput() {
      var val = (input.value || '').trim();
      if (!val) return;
      input.value = '';
      appendMsg('user', esc(val));

      var q = val.toLowerCase();
      if (/workshop|python.*99|7.*day|challenge/.test(q)) {
        handleIntent('workshops');
      } else if (/fee|cost|price|emi|pay/.test(q)) {
        handleIntent('fees');
      } else if (/whatsapp|counselor|mentor|call|phone|contact/.test(q)) {
        handleIntent('whatsapp');
      } else if (/python/.test(q)) {
        searchCoursesByTopic('python');
      } else if (/react|frontend|web|mern|full.*stack/.test(q)) {
        searchCoursesByTopic('web');
      } else if (/ai|machine|gpt|agent/.test(q)) {
        searchCoursesByTopic('ai');
      } else if (/data|analytics|sql/.test(q)) {
        searchCoursesByTopic('data');
      } else {
        searchCoursesByTopic(val);
      }
    }

    send.addEventListener('click', handleUserInput);
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') handleUserInput();
    });
  }

  if (document.readyState !== 'loading') initCai();
  else document.addEventListener('DOMContentLoaded', initCai);
})();
