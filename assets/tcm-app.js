/*
 * The Code Munk — front-end integration for the static marketing site.
 * Adds backend functionality WITHOUT changing the existing design:
 *   - auth-aware "Join Now" (opens the designer's modal, or links to the
 *     dashboard when already signed in)
 *   - wires the auth modal's password Login/Register to the real PHP backend
 *   - contact form submission to the API
 *   - WhatsApp click-to-chat on any [data-wa] element
 *   - live hydration of course/event listings from the API, reusing the
 *     original card markup (containers marked with data-tcm-list)
 * Every hook is defensive and no-ops if its target isn't present.
 */
(function () {
    "use strict";

    // Detect the application base path.
    // Priority: 1) window.TCM_BASE  2) <meta name="app-base">  3) auto-detect from script URL
    var BASE = (function () {
        if (window.TCM_BASE) return window.TCM_BASE.replace(/\/$/, "");
        var meta = document.querySelector('meta[name="app-base"]');
        if (meta && meta.content !== "") return meta.content.replace(/\/$/, "");
        // Auto-detect: find the <script src="...tcm-app.js"> and strip /assets/tcm-app.js
        var scripts = document.getElementsByTagName("script");
        for (var i = 0; i < scripts.length; i++) {
            var src = scripts[i].src || "";
            var marker = src.indexOf("/assets/tcm-app.js");
            if (marker !== -1) {
                // src is absolute e.g. http://localhost/tcm/tcm-2.0/assets/tcm-app.js
                // extract path portion up to the marker
                var pathPart = src.substring(src.indexOf("//") + 2);  // strip "http://"
                pathPart = pathPart.substring(pathPart.indexOf("/"));  // strip host
                return pathPart.substring(0, pathPart.indexOf("/assets/tcm-app.js")).replace(/\/$/, "");
            }
        }
        return "";
    })();
    var api = function (p) { return BASE + p; };
    var ME = { authenticated: false, dashboard_url: null, whatsapp_number: "", csrf_token: "" };

    function ready(fn) {
        if (document.readyState !== "loading") fn();
        else document.addEventListener("DOMContentLoaded", fn);
    }
    function $(id) { return document.getElementById(id); }
    function esc(s) {
        return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
            return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
        });
    }
    function getJSON(url) {
        return fetch(url, { headers: { Accept: "application/json" }, credentials: "same-origin" })
            .then(function (r) { return r.json(); });
    }
    function postJSON(url, data) {
        return fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-Token": ME.csrf_token || "",
            },
            credentials: "same-origin",
            body: JSON.stringify(data),
        }).then(function (r) { return r.json().catch(function () { return { success: false, message: "Unexpected response." }; }); });
    }

    /* ---- formatting helpers ------------------------------------------- */
    var MONTHS = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    function fmtDate(d) {
        if (!d) return "TBA";
        var p = d.split("-"); if (p.length !== 3) return d;
        return parseInt(p[2], 10) + " " + MONTHS[parseInt(p[1], 10) - 1] + " " + p[0];
    }
    function fmtTime(t) {
        if (!t) return "";
        var p = t.split(":"); var h = parseInt(p[0], 10); var m = p[1] || "00";
        var ap = h >= 12 ? "PM" : "AM"; var hh = h % 12 || 12;
        return (hh < 10 ? "0" + hh : hh) + ":" + m + " " + ap;
    }
    function money(n) { return "\u20B9" + Number(n || 0).toLocaleString("en-IN"); }
    var CAT = { frontend: "Frontend", backend: "Backend", python: "Python", dsa: "DSA", career: "Career", general: "General" };
    function catLabel(c) { return CAT[c] || (c ? c.charAt(0).toUpperCase() + c.slice(1) : ""); }

    /* ================= AUTH STATE + NAV ================================ */
    function applyAuthState() {
        if (!ME.authenticated) return;

        // Replace every .join-btn with a proper link based on role
        document.querySelectorAll(".join-btn").forEach(function (el) {
            var clone = el.cloneNode(true);
            clone.textContent = ME.role === "admin" ? "Admin Panel" : "My Dashboard";
            clone.setAttribute("href", api(ME.dashboard_url || "/student"));
            clone.style.cssText += ";background:#111;color:#fff;";
            if (el.parentNode) el.parentNode.replaceChild(clone, el);
        });

        // Also hide auth overlay trigger links that have data-auth-trigger
        document.querySelectorAll("[data-auth-trigger]").forEach(function (el) {
            el.setAttribute("href", api(ME.dashboard_url || "/student"));
        });
    }

    /* ================= AUTH MODAL -> REAL BACKEND ====================== */
    function modalNotice(msg, ok) {
        var view = document.querySelector(".auth-view.active") || document.querySelector(".auth-modal-body");
        if (!view) { alert(msg); return; }
        var box = view.querySelector(".tcm-auth-msg");
        if (!box) {
            box = document.createElement("div");
            box.className = "tcm-auth-msg";
            box.style.cssText = "margin:10px 0;padding:10px 12px;border-radius:10px;font-size:.85rem;";
            view.insertBefore(box, view.firstChild);
        }
        box.style.background = ok ? "#e7faf4" : "#ffeaea";
        box.style.color = ok ? "#00a884" : "#e23";
        box.textContent = msg;
    }
    function busy(btn, on) {
        if (!btn) return;
        btn.disabled = on;
        btn.classList.toggle("loading", on);
    }
    // Replace a node to drop listeners added by auth.js, returning the fresh node.
    function rebind(id) {
        var el = $(id);
        if (!el) return null;
        var fresh = el.cloneNode(true);
        el.parentNode.replaceChild(fresh, el);
        return fresh;
    }
    function wireAuthModal() {
        if (!document.getElementById("authOverlay")) return; // modal not on page

        var loginBtn = rebind("loginSubmitBtn");
        if (loginBtn) {
            loginBtn.addEventListener("click", function (e) {
                e.preventDefault();
                var email = (($("landing-pw-email") || {}).value || ($("regEmail") || {}).value || "").trim();
                var pw = (($("loginPassword") || {}).value || "");
                if (!pw) { modalNotice("Please enter your password.", false); return; }
                busy(loginBtn, true);
                postJSON(api("/auth/login"), { email: email, password: pw })
                    .then(function (res) {
                        if (res.success && res.data && res.data.redirect) { window.location = res.data.redirect; }
                        else { modalNotice(res.message || "Login failed.", false); busy(loginBtn, false); }
                    })
                    .catch(function () { modalNotice("Network error. Try again.", false); busy(loginBtn, false); });
            });
        }

        var regBtn = rebind("registerBtn");
        if (regBtn) {
            regBtn.addEventListener("click", function (e) {
                e.preventDefault();
                var name = (($("regName") || {}).value || "").trim();
                var email = (($("regEmail") || {}).value || "").trim();
                var pw = (($("regPassword") || {}).value || "");
                if (!name) { modalNotice("Please enter your name.", false); return; }
                if (pw.length < 8) { modalNotice("Password must be at least 8 characters.", false); return; }
                busy(regBtn, true);
                postJSON(api("/auth/register"), { name: name, email: email, password: pw, password_confirmation: pw })
                    .then(function (res) {
                        if (res.success && res.data && res.data.redirect) { window.location = res.data.redirect; }
                        else {
                            var msg = res.message || "Registration failed.";
                            if (res.errors) { var k = Object.keys(res.errors)[0]; if (k) msg = res.errors[k][0]; }
                            modalNotice(msg, false); busy(regBtn, false);
                        }
                    })
                    .catch(function () { modalNotice("Network error. Try again.", false); busy(regBtn, false); });
            });
        }
    }

    // Mimic auth.js view switching so we can drive flows after rebinding.
    function showAuthView(name) {
        document.querySelectorAll(".auth-view").forEach(function (v) { v.classList.remove("active"); });
        var el = $("view-" + name);
        if (el) el.classList.add("active");
    }
    function collectOtp(scopeSel) {
        var inputs = document.querySelectorAll(scopeSel + " input");
        return Array.prototype.map.call(inputs, function (i) { return (i.value || "").trim(); }).join("");
    }

    function wireAuthExtras() {
        if (!$("authOverlay")) return;

        // OTP login: send code (let auth.js handle the view switch), then verify for real.
        var sendOtp = $("landingSendOtp");
        if (sendOtp) {
            sendOtp.addEventListener("click", function () {
                var email = (($("landing-email") || {}).value || "").trim();
                if (!email) return;
                postJSON(api("/auth/otp/request"), { email: email })
                    .then(function (res) { if (!res.success) modalNotice(res.message || "Could not send code.", false); })
                    .catch(function () {});
            });
        }
        var verifyBtn = rebind("verifyOtpBtn");
        if (verifyBtn) {
            verifyBtn.addEventListener("click", function (e) {
                e.preventDefault();
                var email = (($("landing-email") || {}).value || "").trim();
                var code = collectOtp("#view-email-otp .auth-otp-wrap") || collectOtp("#otpInputs");
                if (code.length < 6) { modalNotice("Enter the 6-digit code.", false); return; }
                busy(verifyBtn, true);
                postJSON(api("/auth/otp/verify"), { email: email, otp: code })
                    .then(function (res) {
                        if (res.success && res.data && res.data.redirect) { window.location = res.data.redirect; }
                        else { modalNotice(res.message || "Invalid code.", false); busy(verifyBtn, false); }
                    })
                    .catch(function () { modalNotice("Network error.", false); busy(verifyBtn, false); });
            });
        }

        // Forgot password: send reset code (auth.js switches view), then reset for real.
        var forgotSend = $("forgotSendOtp");
        if (forgotSend) {
            forgotSend.addEventListener("click", function () {
                var email = (($("forgotEmail") || {}).value || "").trim();
                if (!email) return;
                postJSON(api("/auth/password/request"), { email: email }).catch(function () {});
            });
        }
        var resetBtn = rebind("resetPasswordBtn");
        if (resetBtn) {
            resetBtn.addEventListener("click", function (e) {
                e.preventDefault();
                var email = (($("forgotEmail") || {}).value || "").trim();
                var code = collectOtp("#view-forgot-otp .auth-otp-wrap") || collectOtp("#forgotOtpInputs");
                var pw = (($("newPassword") || {}).value || "");
                var conf = (($("confirmPassword") || {}).value || "");
                if (code.length < 6) { modalNotice("Enter the 6-digit code.", false); return; }
                if (pw.length < 8) { modalNotice("Password must be at least 8 characters.", false); return; }
                if (pw !== conf) { modalNotice("Passwords do not match.", false); return; }
                busy(resetBtn, true);
                postJSON(api("/auth/password/reset"), { email: email, otp: code, password: pw })
                    .then(function (res) {
                        if (res.success && res.data && res.data.redirect) { window.location = res.data.redirect; }
                        else { modalNotice(res.message || "Could not reset password.", false); busy(resetBtn, false); }
                    })
                    .catch(function () { modalNotice("Network error.", false); busy(resetBtn, false); });
            });
        }
    }

    /* ================= WHATSAPP ======================================== */
    function waLink(message) {
        var num = (ME.whatsapp_number || "").replace(/\D+/g, "");
        return (num ? "https://wa.me/" + num : "https://wa.me/") + "?text=" + encodeURIComponent(message || "Hi The Code Munk!");
    }
    function wireWhatsApp() {
        document.querySelectorAll("[data-wa]").forEach(function (el) {
            el.addEventListener("click", function (e) { e.preventDefault(); window.open(waLink(el.getAttribute("data-wa")), "_blank"); });
        });
    }

    /* ================= CONTACT FORM ==================================== */
    function wireContactForm() {
        var form = $("contactForm");
        if (!form) return;
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            var val = function (id) { var el = $(id); return el ? el.value.trim() : ""; };
            var payload = {
                name: (val("firstName") + " " + val("lastName")).trim() || val("name"),
                email: val("email"),
                phone: val("phone"),
                subject: val("topic") || val("subject"),
                message: val("message"),
            };
            var btn = form.querySelector('button[type="submit"], button');
            var label = btn ? btn.textContent : "";
            if (btn) { btn.disabled = true; btn.textContent = "Sending..."; }
            postJSON(api("/api/contact"), payload)
                .then(function (res) {
                    contactNotice(form, res.success, res.message || (res.success ? "Sent!" : "Something went wrong."));
                    if (res.success) form.reset();
                })
                .catch(function () { contactNotice(form, false, "Network error. Please try again."); })
                .finally(function () { if (btn) { btn.disabled = false; btn.textContent = label; } });
        });
    }
    function contactNotice(form, ok, msg) {
        var box = form.querySelector(".tcm-notice");
        if (!box) {
            box = document.createElement("div");
            box.className = "tcm-notice";
            box.style.cssText = "margin:14px 0;padding:12px 16px;border-radius:10px;font-size:.92rem;";
            form.prepend(box);
        }
        box.style.background = ok ? "#e7faf4" : "#ffeaea";
        box.style.color = ok ? "#00a884" : "#e23";
        box.textContent = msg;
    }

    /* ================= LISTING HYDRATION =============================== */

    // insights.html blog post card
    function postCard(p) {
        var date = p.published_at ? (function(d) {
            var dt = new Date(d);
            return dt.toLocaleDateString("en-IN", {day:"numeric", month:"short", year:"numeric"});
        })(p.published_at) : "";
        var tags = p.tags ? p.tags.split(",").map(function(t) {
            return '<span class="ins-tag">#' + esc(t.trim()) + '</span>';
        }).join("") : "";
        return '<div class="ins-article-card">' +
            '<div class="ins-article-meta">' +
                '<span class="ins-article-cat">' + esc(p.category || "General") + '</span>' +
                (date ? ' · <span class="ins-article-date">' + esc(date) + '</span>' : '') +
            '</div>' +
            '<h3 class="ins-article-title">' + esc(p.title) + '</h3>' +
            '<p class="ins-article-excerpt">' + esc(p.excerpt || "") + '</p>' +
            (tags ? '<div class="ins-article-tags">' + tags + '</div>' : '') +
        '</div>';
    }

    // index.html course card
    var allGifs = [
        "coding-fullstack.gif",
        "coding-frontend.gif",
        "coding-python.gif",
        "coding-database.gif",
        "coding-algorithm.gif",
        "coding-monitor.gif",
        "coding-basics.gif",
        "coding-webdev.gif",
        "coding-ai.gif",
        "coding-career.gif",
        "coding-automation.gif",
        "coding-analytics.gif"
    ];
    var usedGifs = new Set();

    function getUniqueGifForCourse(slug, index) {
        var gifMap = {
            "full-stack-development": "coding-fullstack.gif",
            "frontend-development": "coding-monitor.gif",
            "python-development": "coding-python.gif",
            "database-systems": "coding-database.gif",
            "dsa-problem-solving": "coding-algorithm.gif",
            "cloud-fundamentals": "coding-frontend.gif",
            "computer-basics": "coding-basics.gif",
            "web-development-basics": "coding-webdev.gif",
            "ai-tools-mastery": "coding-ai.gif",
            "career-transition-program": "coding-career.gif",
            "ai-automation": "coding-automation.gif",
            "data-analytics": "coding-analytics.gif"
        };
        var preferred = gifMap[slug];
        if (preferred && !usedGifs.has(preferred)) {
            usedGifs.add(preferred);
            return preferred;
        }
        for (var i = 0; i < allGifs.length; i++) {
            if (!usedGifs.has(allGifs[i])) {
                usedGifs.add(allGifs[i]);
                return allGifs[i];
            }
        }
        // Fallback index-based modulo to guarantee cycling if cards exceed 12
        var fallbackIdx = (typeof index === 'number' ? index : 0) % allGifs.length;
        return allGifs[fallbackIdx];
    }

    function normalizeThumbnailUrl(url, slug, idx) {
        if (!url || typeof url !== 'string' || url.trim() === '') {
            return './assets/' + getUniqueGifForCourse(slug, idx);
        }
        var clean = url.trim();
        if (clean.startsWith('http://') || clean.startsWith('https://')) {
            return clean;
        }
        var filename = clean.split('/').pop().split('?')[0];
        // If it points to one of our tech GIF presets or any .gif in assets
        if (allGifs.indexOf(filename) !== -1 || (filename.endsWith('.gif') && clean.indexOf('uploads/') === -1)) {
            return './assets/' + filename;
        }
        // If it is an uploaded course thumbnail/image
        if (clean.indexOf('uploads/') !== -1) {
            var uploadPath = clean.substring(clean.indexOf('uploads/'));
            return api('/' + uploadPath);
        }
        if (clean.startsWith('./') || clean.startsWith('../')) {
            return clean;
        }
        return api(clean.startsWith('/') ? clean : '/' + clean);
    }

    function courseCardHome(c, idx) {
        var price = '<div class="course-price">' + money(c.price) +
            (c.original_price ? " <span>" + money(c.original_price) + "</span>" : "") + "</div>";
        var thumbSrc = normalizeThumbnailUrl(c.thumbnail_url || c.thumbnail, c.slug, idx);
        var thumb = '<div class="course-card-thumb"><img src="' + esc(thumbSrc) + '" alt="' + esc(c.title) + '" data-course-slug="' + esc(c.slug) + '" onerror="this.onerror=null;this.src=\'./assets/coding-fullstack.gif\';"></div>';
        return '<div class="course-card" data-course-slug="' + esc(c.slug) + '" data-course-title="' + esc(c.title) + '">' +
            thumb +
            price +
            "<h3>" + esc(c.title) + "</h3>" +
            "<p>" + esc(c.subtitle || "") + "</p>" +
            '<div class="course-card-actions">' +
                '<a href="' + api('/courses/' + encodeURIComponent(c.slug)) + '" class="btn-roadmap">Get Road Map <i class="bi bi-arrow-right"></i></a>' +
                '<button type="button" class="btn-brochure btn-trigger-brochure" data-course-slug="' + esc(c.slug) + '" data-course-title="' + esc(c.title) + '"><i class="bi bi-file-earmark-pdf"></i> Brochure</button>' +
            '</div>' +
            "</div>";
    }
    // index.html event card (with 16-dot seat visual)
    function eventCardHome(ev) {
        var total = parseInt(ev.total_seats, 10) || 16;
        var filled = parseInt(ev.seats_filled, 10) || 0;
        var dots = "";
        for (var i = 0; i < 16; i++) {
            var on = i < Math.round((filled / total) * 16);
            dots += on ? '<span class="filled"></span>' : "<span></span>";
        }
        var indicator = ev.status === "ongoing"
            ? '<div class="live-indicator"><span class="live-dot"></span> Live Now</div>' : "";
        return '<div class="event-card">' + indicator +
            "<h3>" + esc(ev.title) + "</h3>" +
            "<p>" + esc(ev.description || "") + "</p>" +
            '<div class="seat-visual">' + dots + "</div>" +
            '<div class="seat-count">' + filled + " / " + total + " Seats Filled</div>" +
            '<div class="event-meta">' +
                '<span><i class="bi bi-calendar-event"></i> ' + esc(fmtDate(ev.event_date)) + "</span>" +
                (ev.event_time ? '<span><i class="bi bi-clock"></i> ' + esc(fmtTime(ev.event_time)) + "</span>" : "") +
            "</div>" +
            '<a href="event-details.html?event=' + encodeURIComponent(ev.slug) + '" class="event-link">View Event \u2192</a>' +
            "</div>";
    }
    // programs.html event card
    function eventCardPg(ev) {
        var total = parseInt(ev.total_seats, 10) || 16;
        var filled = parseInt(ev.seats_filled, 10) || 0;
        var pct = total ? Math.round((filled / total) * 100) : 0;
        var isNonTech = ev.category === "jee-neet" || ev.category === "govt" || ev.status === "coming_soon";
        var statusBadge = isNonTech
            ? '<div class="pg-status-badge upcoming" style="background:#fff7ed; color:#c2410c; border:1px solid #ffedd5;">Coming Soon</div>'
            : ev.status === "ongoing"
            ? '<div class="pg-status-badge ongoing"><span class="pg-live-dot"></span> Live Now</div>'
            : ev.status === "upcoming"
                ? '<div class="pg-status-badge upcoming">Upcoming</div>'
                : '<div class="pg-status-badge past">Completed</div>';
        var btn = isNonTech
            ? '<button class="pg-card-btn secondary disabled" disabled style="opacity:0.75; cursor:not-allowed;">Coming Soon <i class="bi bi-clock-history"></i></button>'
            : ev.status === "past"
            ? '<a href="event-details.html?event=' + encodeURIComponent(ev.slug) + '" class="pg-card-btn secondary">View Recording <i class="bi bi-play-circle"></i></a>'
            : '<a href="event-details.html?event=' + encodeURIComponent(ev.slug) + '" class="pg-card-btn">View Event <i class="bi bi-arrow-right"></i></a>';
        return '<div class="pg-event-card" data-status="' + esc(isNonTech ? "upcoming" : ev.status) + '" data-type="' + esc(ev.type) +
            '" data-cat="' + esc(ev.category) + '" data-title="' + esc((ev.title || "").toLowerCase()) + '">' +
            '<div class="pg-card-top">' + statusBadge +
                '<div class="pg-type-badge ' + esc(ev.type) + '">' + (ev.type === "free" ? "Free" : "Paid") + "</div></div>" +
            '<div class="pg-card-cat">' + esc(catLabel(ev.category)) + "</div>" +
            "<h3>" + esc(ev.title) + "</h3>" +
            "<p>" + esc(ev.description || "") + "</p>" +
            '<div class="pg-card-meta">' +
                '<span><i class="bi bi-calendar-event"></i> ' + esc(fmtDate(ev.event_date)) + "</span>" +
                (ev.event_time ? '<span><i class="bi bi-clock"></i> ' + esc(fmtTime(ev.event_time)) + "</span>" : "") +
                '<span><i class="bi bi-laptop"></i> ' + esc((ev.mode || "online").charAt(0).toUpperCase() + (ev.mode || "online").slice(1)) + "</span>" +
            "</div>" + btn + "</div>";
    }

    function hydrateOne(container) {
        var type = container.getAttribute("data-tcm-list");
        var tpl = container.getAttribute("data-tcm-tpl") || "home";
        var qs = [];
        if (container.getAttribute("data-audience")) qs.push("audience=" + encodeURIComponent(container.getAttribute("data-audience")));
        if (container.getAttribute("data-status")) qs.push("status=" + encodeURIComponent(container.getAttribute("data-status")));
        var endpoint = type === "courses" ? "/api/courses"
            : type === "programs" ? "/api/programs"
            : type === "posts" ? "/api/posts"
            : "/api/events";
        var url = api(endpoint + (qs.length ? "?" + qs.join("&") : ""));

        getJSON(url).then(function (res) {
            if (!res || !res.success || !Array.isArray(res.data)) return;
            var items = res.data;
            if (!items.length) return; // keep the designer's sample cards if API empty
            var html = items.map(function (item, i) {
                if (type === "courses") return courseCardHome(item, i);
                if (type === "posts")   return postCard(item);
                return tpl === "pg" ? eventCardPg(item) : eventCardHome(item);
            }).join("");
            container.innerHTML = html;

            // Refresh the programs.html results counter if present.
            var counter = document.getElementById("resultsCount");
            if (counter && tpl === "pg") {
                counter.textContent = items.length + " event" + (items.length !== 1 ? "s" : "") + " found";
            }
        }).catch(function () {});
    }
    function hydrateLists() {
        document.querySelectorAll("[data-tcm-list]").forEach(hydrateOne);
    }

    /* ================= DETAIL PAGE HYDRATION =========================== */
    function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
    function qsa(sel, ctx) { return (ctx || document).querySelectorAll(sel); }

    function setTxt(sel, val, ctx) {
        var el = qs(sel, ctx);
        if (el) el.textContent = val;
    }

    /* ---------- course-details.html ---------- */
    function hydrateCourseDetail() {
        var params = new URLSearchParams(window.location.search);
        var slug = params.get("course");
        if (!slug) return;

        getJSON(api("/api/courses/" + encodeURIComponent(slug))).then(function (res) {
            if (!res || !res.success || !res.data) return;
            var c = res.data;

            // document title
            document.title = (c.title || "Course") + " \u2013 The Code Munk";

            // breadcrumb
            var bc = qs(".cd-breadcrumb span:last-child");
            if (bc) bc.textContent = c.title || "";

            // hero h1
            var h1 = qs(".cd-hero-content h1");
            if (h1) h1.textContent = c.title || "";

            // hero desc
            setTxt(".cd-hero-desc", c.description || "");

            // price block
            setTxt(".cd-price", c.price != null ? money(c.price) : "");
            setTxt(".cd-price-original", c.original_price ? money(c.original_price) : "");
            var disc = c.discount_percent != null
                ? c.discount_percent
                : (c.original_price && c.price && c.original_price > 0
                    ? Math.round((1 - c.price / c.original_price) * 100)
                    : 0);
            setTxt(".cd-discount-badge", disc ? disc + "% Off" : "");

            // seats bar
            var total = parseInt(c.total_seats, 10) || 0;
            var filled = parseInt(c.seats_filled, 10) || 0;
            var left = Math.max(0, total - filled);
            var pct = total > 0 ? ((filled / total) * 100).toFixed(0) : 0;
            var fill = qs(".cd-seats-fill");
            if (fill) fill.style.width = pct + "%";
            var seatP = qs(".cd-seats p");
            if (seatP) seatP.innerHTML = "<strong>" + left + " seats left</strong> out of " + total;

            // stats row — rating, students_count, duration, "Live"
            var stats = qsa(".cd-stat strong");
            if (stats[0]) stats[0].textContent = c.rating != null ? c.rating : "—";
            if (stats[1]) stats[1].textContent = c.students_count != null ? Number(c.students_count).toLocaleString("en-IN") + "+" : "—";
            if (stats[2]) stats[2].textContent = c.duration || "—";
            if (stats[3]) stats[3].textContent = "Live";

            // meta row divs
            var metas = qsa(".cd-meta-row div");
            if (metas[0]) metas[0].innerHTML = '<i class="bi bi-calendar-event"></i> Starts ' + (c.starts_at ? fmtDate(c.starts_at.split(" ")[0]) : "TBA");
            if (metas[1]) metas[1].innerHTML = '<i class="bi bi-clock"></i> ' + (c.schedule || "Schedule TBA");
            if (metas[2]) metas[2].innerHTML = '<i class="bi bi-translate"></i> ' + (c.language || "English");
            if (metas[3]) metas[3].innerHTML = '<i class="bi bi-patch-check-fill"></i> ' + ((parseInt(c.certificate, 10)) ? "Certificate Included" : "No Certificate");

            // category badge
            var badge = qs(".cd-badge-tag");
            if (badge && c.category_name) badge.innerHTML = '<i class="bi bi-mortarboard-fill"></i> ' + esc(c.category_name);

            // bestseller badge
            var pop = qs(".cd-badge-popular");
            if (pop) pop.style.display = (parseInt(c.is_bestseller, 10) === 1) ? "" : "none";

            // Course GIF thumbnail
            var thumbImg = qs("#cdCourseThumbImg") || qs(".cd-course-thumb img");
            if (thumbImg) {
                var gifSrc = normalizeThumbnailUrl(c.thumbnail_url || c.thumbnail, c.slug || "", 0);
                thumbImg.onerror = function () {
                    this.onerror = null;
                    this.src = './assets/coding-fullstack.gif';
                };
                thumbImg.src = gifSrc;
                thumbImg.alt = c.title || "Course GIF";
            }

            // Share buttons
            var shareBtns = qsa(".tcm-share-btn");
            shareBtns.forEach(function (sBtn) {
                sBtn.setAttribute("data-share-title", (c.title || "Course") + " | TCM One");
                sBtn.setAttribute("data-share-text", c.subtitle || c.description || ("Check out the " + (c.title || "") + " course on TCM One!"));
                sBtn.setAttribute("data-share-url", window.location.href);
            });

            // enroll buttons
            var enrollUrl = (c.slug ? BASE + "/student/courses/" + encodeURIComponent(c.slug) : "#");
            qsa(".cd-enroll-btn").forEach(function (btn) { btn.setAttribute("href", enrollUrl); });
            // also wire quick-action "Enroll" link
            qsa(".action-btn").forEach(function (btn) {
                if ((btn.textContent || "").trim().toLowerCase() === "enroll") {
                    btn.setAttribute("href", enrollUrl);
                }
            });

            // curriculum
            if (Array.isArray(c.curriculum) && c.curriculum.length) {
                var wrapper = qs(".cd-curriculum-wrapper");
                if (wrapper) {
                    var html = c.curriculum.map(function (mod, idx) {
                        var num = String(idx + 1).padStart(2, "0");
                        var lessons = Array.isArray(mod.lessons) ? mod.lessons : [];
                        var lessonHtml = lessons.map(function (l) {
                            var isProject = (l.type === "project");
                            return '<div class="cd-lesson' + (isProject ? " cd-project-lesson" : "") + '">' +
                                '<i class="bi ' + (isProject ? "bi-folder2-open" : "bi-play-circle") + '"></i> ' +
                                '<span>' + esc(l.title) + '</span>' +
                                '<small' + (isProject ? ' class="project-tag"' : "") + '>' + esc(l.type || "Live") + '</small>' +
                                '</div>';
                        }).join("");
                        return '<details class="cd-module"' + (idx === 0 ? " open" : "") + '>' +
                            '<summary class="cd-module-header">' +
                            '<div class="cd-module-left">' +
                            '<div class="cd-module-num">' + num + '</div>' +
                            '<div><h4>' + esc(mod.title) + '</h4>' +
                            '<span>' + lessons.length + ' sessions</span>' +
                            '</div></div>' +
                            '<i class="bi bi-chevron-down cd-module-arrow"></i>' +
                            '</summary>' +
                            '<div class="cd-module-body">' + lessonHtml + '</div>' +
                            '</details>';
                    }).join("");
                    wrapper.innerHTML = html;
                }
            }
        }).catch(function () {});
    }

    /* ---------- event-details.html ---------- */
    function hydrateEventDetail() {
        var params = new URLSearchParams(window.location.search);
        var slug = params.get("event");
        if (!slug) return;

        getJSON(api("/api/events/" + encodeURIComponent(slug))).then(function (res) {
            if (!res || !res.success || !res.data) return;
            var ev = res.data;

            // document title
            document.title = (ev.title || "Event") + " \u2013 The Code Munk";

            // hero content
            var heroContent = qs(".hero-content");
            if (heroContent) {
                var heroH1 = qs("h1", heroContent);
                if (heroH1) heroH1.textContent = ev.title || "";
                var heroP = qs("p", heroContent);
                if (heroP) heroP.textContent = ev.description || "";
            }

            // hero meta divs
            var metaDivs = qsa(".hero-meta div");
            if (metaDivs[0]) metaDivs[0].innerHTML = '<i class="bi bi-calendar-event"></i> ' + fmtDate(ev.event_date);
            if (metaDivs[1]) metaDivs[1].innerHTML = '<i class="bi bi-clock"></i> ' + (ev.event_time ? fmtTime(ev.event_time) : "TBA");
            if (metaDivs[2]) metaDivs[2].innerHTML = '<i class="bi bi-laptop"></i> ' + (ev.mode ? ev.mode.charAt(0).toUpperCase() + ev.mode.slice(1) : "Online");

            // availability card
            var total = parseInt(ev.total_seats, 10) || 16;
            var filled = parseInt(ev.seats_filled, 10) || 0;
            var left = Math.max(0, total - filled);
            var pct = total > 0 ? Math.round((filled / total) * 100) : 0;

            var avH3 = qs(".availability-card h3");
            if (avH3) avH3.textContent = pct + "% Filled";

            // rebuild dots grid
            var grid = qs(".availability-grid");
            if (grid) {
                var gridDots = "";
                var dotTotal = Math.min(total, 32); // cap visual at 32
                var dotFilled = Math.round((filled / total) * dotTotal);
                for (var d = 0; d < dotTotal; d++) {
                    gridDots += '<span class="dot' + (d < dotFilled ? " filled" : "") + '"></span>';
                }
                grid.innerHTML = gridDots;
            }

            // stats strongs
            var avStats = qsa(".availability-stats strong");
            if (avStats[0]) avStats[0].textContent = filled;
            if (avStats[1]) avStats[1].textContent = left;

            // status badge
            var isFull = left <= 0 && ev.status !== "past";
            var statusTxt = ev.status === "past"
                ? "Event Completed"
                : (isFull ? "Event Full" : "Registration Open");
            var statusBadge = qs(".status-badge");
            if (statusBadge) {
                statusBadge.textContent = statusTxt;
                statusBadge.className = "status-badge" +
                    (ev.status === "past" ? " past" : (isFull ? " full" : ""));
            }

            // register buttons — all .btn-dark links
            qsa(".btn-dark").forEach(function (btn) {
                if (ev.status === "past") {
                    btn.textContent = "Event Ended";
                    btn.removeAttribute("href");
                    btn.style.opacity = "0.5";
                    btn.style.cursor = "default";
                } else if (isFull) {
                    btn.textContent = "Event Full";
                    btn.removeAttribute("href");
                    btn.style.opacity = "0.5";
                    btn.style.cursor = "default";
                } else if (ev.type === "free") {
                    btn.textContent = "Register Free";
                    btn.setAttribute("href", BASE + "/student/events/" + encodeURIComponent(ev.id) + "/join");
                } else {
                    var waMsg = "Hi! I want to register for the event: " + (ev.title || slug);
                    btn.textContent = "Register \u2013 " + money(ev.price || 0);
                    btn.setAttribute("href", waLink(waMsg));
                    btn.setAttribute("target", "_blank");
                }
            });

            // also wire action-card "Register" button
            qsa(".action-btn").forEach(function (btn) {
                var txt = (btn.textContent || "").trim().toLowerCase();
                if (txt === "register") {
                    if (ev.status === "past" || isFull) {
                        btn.style.opacity = "0.5";
                        btn.removeAttribute("href");
                    } else if (ev.type === "free") {
                        btn.setAttribute("href", BASE + "/student/events/" + encodeURIComponent(ev.id) + "/join");
                    } else {
                        btn.setAttribute("href", waLink("Hi! I want to register for: " + (ev.title || slug)));
                        btn.setAttribute("target", "_blank");
                    }
                }
            });
        }).catch(function () {});
    }

    function hydrateDetailPage() {
        // Detect which detail page we're on by URL path or page elements
        var path = window.location.pathname;
        var isCoursePage = path.indexOf("course-details") !== -1
            || document.querySelector("[data-course-detail]") !== null
            || (new URLSearchParams(window.location.search)).get("course") !== null;
        var isEventPage = path.indexOf("event-details") !== -1
            || document.querySelector("[data-event-detail]") !== null
            || (new URLSearchParams(window.location.search)).get("event") !== null;

        if (isCoursePage && !isEventPage) hydrateCourseDetail();
        if (isEventPage && !isCoursePage) hydrateEventDetail();
    }

    function bindCourseWhatsAppCTA() {
        var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var now = new Date();
        var currentMonth = months[now.getMonth()];
        var currentYear = now.getFullYear();
        var batchLabel = currentMonth + " Batch " + currentYear;
        var phone = "919238695500";

        // 1. programs.html event cards
        document.querySelectorAll('.pg-event-card').forEach(function (card) {
            var titleEl = card.querySelector('h3');
            var btn = card.querySelector('.pg-card-btn');
            if (titleEl && btn && btn.tagName === 'A') {
                var courseTitle = titleEl.textContent.trim();
                var msg = "Hello TCM One! I am interested in getting the roadmap for " + courseTitle + " (" + batchLabel + "). Please share program syllabus & details.";
                btn.href = "https://wa.me/" + phone + "?text=" + encodeURIComponent(msg);
                btn.target = "_blank";
                btn.rel = "noopener";
                btn.innerHTML = 'Get Road Map <i class="bi bi-arrow-right"></i>';
            }
        });

        // 2. index.html course cards
        document.querySelectorAll('.course-card').forEach(function (card) {
            var titleEl = card.querySelector('h3');
            var btn = card.querySelector('.btn-roadmap') || card.querySelector('a:not(.btn-brochure)');
            if (titleEl && btn) {
                var courseTitle = titleEl.textContent.trim();
                var msg = "Hello TCM One! I am interested in getting the roadmap for " + courseTitle + " (" + batchLabel + "). Please share program syllabus & details.";
                btn.href = "https://wa.me/" + phone + "?text=" + encodeURIComponent(msg);
                btn.target = "_blank";
                btn.rel = "noopener";
                btn.innerHTML = 'Get Road Map <i class="bi bi-arrow-right"></i>';
            }
        });

        // 3. course-details.html enroll button
        var cdEnrollBtn = document.querySelector('.cd-enroll-btn');
        if (cdEnrollBtn) {
            var cdTitle = document.querySelector('.cd-hero-content h1') ? document.querySelector('.cd-hero-content h1').textContent.trim() : 'Full Stack Development';
            var msg = "Hello TCM One! I am interested in getting the roadmap & enrolling in " + cdTitle + " (" + batchLabel + "). Please share enrollment steps.";
            cdEnrollBtn.href = "https://wa.me/" + phone + "?text=" + encodeURIComponent(msg);
            cdEnrollBtn.target = "_blank";
            cdEnrollBtn.rel = "noopener";
            cdEnrollBtn.innerHTML = 'Get Road Map <i class="bi bi-arrow-right"></i>';
        }
    }

    // Safeguards course card thumbnails so GIFs are never broken or missing
    function deduplicateCourseGifs() {
        var allCourseCards = document.querySelectorAll('.course-card');
        allCourseCards.forEach(function (card, idx) {
            var img = card.querySelector('.course-card-thumb img');
            if (!img) return;
            img.onerror = function () {
                this.onerror = null;
                this.src = './assets/coding-fullstack.gif';
            };
            var currentSrc = img.getAttribute('src') || '';
            if (!currentSrc || currentSrc.trim() === '' || (!currentSrc.includes('.gif') && !currentSrc.includes('.png') && !currentSrc.includes('.jpg') && !currentSrc.includes('.webp'))) {
                var slug = img.getAttribute('data-course-slug') || '';
                img.src = './assets/' + getUniqueGifForCourse(slug, idx);
            }
        });
    }

    /* ================= COURSE BROCHURE LEAD MODAL ===================== */
    function ensureBrochureModal() {
        if (document.getElementById('tcmBrochureModalOverlay')) return;

        var overlay = document.createElement('div');
        overlay.id = 'tcmBrochureModalOverlay';
        overlay.className = 'tcm-brochure-modal-overlay';
        overlay.innerHTML =
            '<div class="tcm-brochure-modal" role="dialog" aria-modal="true" aria-labelledby="tcmBrochureModalTitle">' +
                '<button type="button" class="tcm-brochure-modal-close" id="tcmBrochureModalCloseBtn" aria-label="Close modal">&times;</button>' +
                '<div class="tcm-brochure-modal-badge"><i class="bi bi-file-earmark-pdf"></i> Official Curriculum</div>' +
                '<h3 id="tcmBrochureModalTitle">Download Syllabus &amp; Brochure</h3>' +
                '<p class="course-sub" id="tcmBrochureModalSub">Fill in your details below to instantly download the detailed course curriculum, projects &amp; roadmap.</p>' +
                '<form id="tcmBrochureLeadForm" novalidate>' +
                    '<input type="hidden" id="tcmBrochureCourseSlug" name="course_slug" value="">' +
                    '<div class="form-group">' +
                        '<label for="tcmBrochureName">Your Full Name *</label>' +
                        '<input type="text" class="form-control" id="tcmBrochureName" name="name" placeholder="e.g. Rahul Sharma" required>' +
                    '</div>' +
                    '<div class="form-group">' +
                        '<label for="tcmBrochureEmail">Email Address *</label>' +
                        '<input type="email" class="form-control" id="tcmBrochureEmail" name="email" placeholder="e.g. rahul@example.com" required>' +
                    '</div>' +
                    '<div class="form-group">' +
                        '<label for="tcmBrochurePhone">WhatsApp / Mobile Number *</label>' +
                        '<input type="tel" class="form-control" id="tcmBrochurePhone" name="phone" placeholder="e.g. 9876543210" maxlength="14" required>' +
                    '</div>' +
                    '<div id="tcmBrochureFeedback" class="tcm-brochure-modal-feedback"></div>' +
                    '<button type="submit" class="tcm-brochure-modal-submit" id="tcmBrochureSubmitBtn">' +
                        '<i class="bi bi-download"></i> Download Brochure PDF' +
                    '</button>' +
                    '<div class="tcm-brochure-modal-security">' +
                        '<i class="bi bi-shield-check"></i> 100% spam-free &middot; Direct PDF download starts instantly' +
                    '</div>' +
                '</form>' +
            '</div>';
        document.body.appendChild(overlay);

        // Close handlers
        var closeBtn = document.getElementById('tcmBrochureModalCloseBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeBrochureModal);
        }
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeBrochureModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('active')) {
                closeBrochureModal();
            }
        });

        // Form submission
        var form = document.getElementById('tcmBrochureLeadForm');
        if (form) {
            form.addEventListener('submit', handleBrochureFormSubmit);
        }
    }

    function openBrochureModal(slug, title) {
        ensureBrochureModal();
        var overlay = document.getElementById('tcmBrochureModalOverlay');
        var titleEl = document.getElementById('tcmBrochureModalTitle');
        var subEl = document.getElementById('tcmBrochureModalSub');
        var slugInput = document.getElementById('tcmBrochureCourseSlug');
        var feedback = document.getElementById('tcmBrochureFeedback');

        if (slugInput) slugInput.value = slug || '';
        if (titleEl) {
            titleEl.textContent = title ? title + ' Brochure' : 'Download Course Brochure';
        }
        if (subEl) {
            subEl.textContent = title
                ? 'Enter your details to instantly receive the complete curriculum, projects & syllabus for ' + title + '.'
                : 'Enter your details to instantly download the official program syllabus and roadmap.';
        }
        if (feedback) {
            feedback.className = 'tcm-brochure-modal-feedback';
            feedback.textContent = '';
            feedback.style.display = 'none';
        }

        // Autofill if user already logged in or stored
        var nameInput = document.getElementById('tcmBrochureName');
        var emailInput = document.getElementById('tcmBrochureEmail');
        var phoneInput = document.getElementById('tcmBrochurePhone');
        if (ME && ME.authenticated) {
            if (nameInput && !nameInput.value) nameInput.value = ME.name || '';
            if (emailInput && !emailInput.value) emailInput.value = ME.email || '';
        }
        try {
            var savedName = localStorage.getItem('tcm_lead_name');
            var savedEmail = localStorage.getItem('tcm_lead_email');
            var savedPhone = localStorage.getItem('tcm_lead_phone');
            if (savedName && nameInput && !nameInput.value) nameInput.value = savedName;
            if (savedEmail && emailInput && !emailInput.value) emailInput.value = savedEmail;
            if (savedPhone && phoneInput && !phoneInput.value) phoneInput.value = savedPhone;
        } catch (e) {}

        if (overlay) {
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(function () {
                var firstInput = (nameInput && !nameInput.value) ? nameInput : (phoneInput && !phoneInput.value ? phoneInput : emailInput);
                if (firstInput) firstInput.focus();
            }, 100);
        }
    }

    function closeBrochureModal() {
        var overlay = document.getElementById('tcmBrochureModalOverlay');
        if (overlay) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleBrochureFormSubmit(e) {
        e.preventDefault();
        var nameInput = document.getElementById('tcmBrochureName');
        var emailInput = document.getElementById('tcmBrochureEmail');
        var phoneInput = document.getElementById('tcmBrochurePhone');
        var slugInput = document.getElementById('tcmBrochureCourseSlug');
        var submitBtn = document.getElementById('tcmBrochureSubmitBtn');
        var feedback = document.getElementById('tcmBrochureFeedback');

        var name = (nameInput ? nameInput.value : '').trim();
        var email = (emailInput ? emailInput.value : '').trim();
        var phone = (phoneInput ? phoneInput.value : '').trim();
        var slug = (slugInput ? slugInput.value : '').trim();

        function showError(msg) {
            if (!feedback) return;
            feedback.className = 'tcm-brochure-modal-feedback error';
            feedback.textContent = msg;
            feedback.style.display = 'block';
        }

        if (!name) {
            showError('Please enter your full name.');
            if (nameInput) nameInput.focus();
            return;
        }
        if (!email || !email.includes('@') || !email.includes('.')) {
            showError('Please enter a valid email address.');
            if (emailInput) emailInput.focus();
            return;
        }
        var cleanPhone = phone.replace(/[^\d]/g, '');
        if (cleanPhone.length < 10) {
            showError('Please enter a valid 10-digit mobile number.');
            if (phoneInput) phoneInput.focus();
            return;
        }

        // Save locally for quick autofill next time
        try {
            localStorage.setItem('tcm_lead_name', name);
            localStorage.setItem('tcm_lead_email', email);
            localStorage.setItem('tcm_lead_phone', phone);
        } catch (err) {}

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width:14px;height:14px;border-width:2px;"></span> Generating Download...';
        }

        postJSON(api('/api/courses/brochure-lead'), {
            name: name,
            email: email,
            phone: phone,
            course_slug: slug
        }).then(function (res) {
            if (res && res.success && res.data && res.data.download_url) {
                if (feedback) {
                    feedback.className = 'tcm-brochure-modal-feedback success';
                    feedback.innerHTML = '<i class="bi bi-check-circle"></i> Success! Starting brochure download now...';
                    feedback.style.display = 'block';
                }

                // Trigger file download via invisible link
                var downloadLink = document.createElement('a');
                downloadLink.href = res.data.download_url;
                downloadLink.setAttribute('download', '');
                downloadLink.target = '_blank';
                document.body.appendChild(downloadLink);
                downloadLink.click();
                setTimeout(function () {
                    if (downloadLink.parentNode) downloadLink.parentNode.removeChild(downloadLink);
                }, 1000);

                setTimeout(function () {
                    closeBrochureModal();
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-download"></i> Download Brochure PDF';
                    }
                }, 1600);
            } else {
                showError((res && res.message) ? res.message : 'Unable to initiate download. Please try again.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-download"></i> Download Brochure PDF';
                }
            }
        }).catch(function (err) {
            showError('Network error. Please check your connection and try again.');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-download"></i> Download Brochure PDF';
            }
        });
    }

    function wireBrochureTriggers() {
        // Global click delegation for all brochure triggers across any cards or detail pages
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('.btn-trigger-brochure, .cd-brochure-btn, .ws-btn-brochure');
            if (!trigger) return;

            e.preventDefault();

            // Extract course slug and title
            var slug = trigger.getAttribute('data-course-slug') || '';
            var title = trigger.getAttribute('data-course-title') || '';

            if (!slug) {
                // Try from closest course-card
                var card = trigger.closest('.course-card');
                if (card) {
                    slug = card.getAttribute('data-course-slug') || '';
                    if (!title) {
                        var h3 = card.querySelector('h3');
                        if (h3) title = h3.textContent.trim();
                    }
                }
            }

            // If on course-details.html or show.php without explicit slug attribute
            if (!slug) {
                var urlParams = new URLSearchParams(window.location.search);
                slug = urlParams.get('course') || '';
                if (!slug) {
                    var pathParts = window.location.pathname.split('/').filter(Boolean);
                    var cIdx = pathParts.indexOf('courses');
                    if (cIdx !== -1 && pathParts[cIdx + 1]) {
                        slug = pathParts[cIdx + 1];
                    }
                }
            }

            if (!title) {
                var heroH1 = document.querySelector('.cd-hero-content h1, .ws-detail-hero h1');
                if (heroH1) title = heroH1.textContent.trim();
            }

            openBrochureModal(slug, title);
        });
    /* ================= FEATURED WORKSHOP REGISTRATION MODAL ============ */
    function wireWorkshopModal() {
        var overlay = document.getElementById('tcmWsModalOverlay');
        var closeBtn = document.getElementById('tcmWsModalCloseBtn');
        var form = document.getElementById('tcmWsRegistrationForm');
        var titleEl = document.getElementById('tcmWsModalTitle');
        var badgeEl = document.getElementById('tcmWsPriceBadge');
        var slugInput = document.getElementById('tcmWsSlug');
        var idInput = document.getElementById('tcmWsId');
        var nameInput = document.getElementById('tcmWsFullName');
        var emailInput = document.getElementById('tcmWsEmail');
        var phoneInput = document.getElementById('tcmWsPhone');
        var feedback = document.getElementById('tcmWsFeedback');
        var submitBtn = document.getElementById('tcmWsSubmitBtn');

        // Extract and remember UTM parameters from current URL
        var urlParams = new URLSearchParams(window.location.search);
        var utmSource = urlParams.get('utm_source') || '';
        var utmMedium = urlParams.get('utm_medium') || '';
        var utmCampaign = urlParams.get('utm_campaign') || '';
        var utmContent = urlParams.get('utm_content') || '';

        function openModal(slug, id, title, price) {
            if (!overlay) return;
            if (slugInput) slugInput.value = slug || '';
            if (idInput) idInput.value = id || '';
            if (titleEl) titleEl.textContent = 'Reserve Seat: ' + (title || 'Live Workshop');
            if (badgeEl) badgeEl.textContent = '₹' + (price || '99') + ' Live Cohort';

            if (feedback) {
                feedback.className = 'tcm-ws-feedback';
                feedback.textContent = '';
                feedback.style.display = 'none';
            }

            // Autofill known user details
            if (ME && ME.authenticated) {
                if (nameInput && !nameInput.value) nameInput.value = ME.name || '';
                if (emailInput && !emailInput.value) emailInput.value = ME.email || '';
            }
            try {
                var sName = localStorage.getItem('tcm_lead_name');
                var sEmail = localStorage.getItem('tcm_lead_email');
                var sPhone = localStorage.getItem('tcm_lead_phone');
                if (sName && nameInput && !nameInput.value) nameInput.value = sName;
                if (sEmail && emailInput && !emailInput.value) emailInput.value = sEmail;
                if (sPhone && phoneInput && !phoneInput.value) phoneInput.value = sPhone;
            } catch (e) {}

            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(function () {
                var target = (nameInput && !nameInput.value) ? nameInput : (phoneInput && !phoneInput.value ? phoneInput : emailInput);
                if (target) target.focus();
            }, 100);
        }

        function closeModal() {
            if (!overlay) return;
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

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

        // Delegate clicks on all .btn-register-workshop buttons
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-register-workshop');
            if (!btn) return;
            e.preventDefault();

            var slug = btn.getAttribute('data-workshop-slug') || '';
            var id = btn.getAttribute('data-workshop-id') || '';
            var title = btn.getAttribute('data-workshop-title') || '';
            var price = btn.getAttribute('data-workshop-price') || '99';

            openModal(slug, id, title, price);
        });

        // Form submission
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var fullName = (nameInput ? nameInput.value : '').trim();
                var email = (emailInput ? emailInput.value : '').trim();
                var phone = (phoneInput ? phoneInput.value : '').trim();
                var statusInput = document.getElementById('tcmWsStatus');
                var cityInput = document.getElementById('tcmWsCity');
                var collegeInput = document.getElementById('tcmWsCollege');

                function showError(msg) {
                    if (!feedback) return;
                    feedback.className = 'tcm-ws-feedback error';
                    feedback.textContent = msg;
                    feedback.style.display = 'block';
                }

                if (!fullName) {
                    showError('Please enter your full name.');
                    if (nameInput) nameInput.focus();
                    return;
                }
                if (!email || !email.includes('@')) {
                    showError('Please enter a valid email address.');
                    if (emailInput) emailInput.focus();
                    return;
                }
                var cleanPhone = phone.replace(/[^\d]/g, '');
                if (cleanPhone.length < 10) {
                    showError('Please enter a valid 10-digit mobile number.');
                    if (phoneInput) phoneInput.focus();
                    return;
                }

                // Cache credentials locally
                try {
                    localStorage.setItem('tcm_lead_name', fullName);
                    localStorage.setItem('tcm_lead_email', email);
                    localStorage.setItem('tcm_lead_phone', phone);
                } catch (err) {}

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width:14px;height:14px;border-width:2px;"></span> Reserving Seat...';
                }

                postJSON(api('/api/workshops/register'), {
                    full_name: fullName,
                    email: email,
                    phone: phone,
                    workshop_slug: slugInput ? slugInput.value : '',
                    workshop_id: idInput ? idInput.value : '',
                    current_status: statusInput ? statusInput.value : 'student',
                    city: cityInput ? cityInput.value : '',
                    college: collegeInput ? collegeInput.value : '',
                    utm_source: utmSource,
                    utm_medium: utmMedium,
                    utm_campaign: utmCampaign,
                    utm_content: utmContent,
                    landing_page: window.location.pathname
                }).then(function (res) {
                    if (res && res.success) {
                        if (feedback) {
                            feedback.className = 'tcm-ws-feedback success';
                            feedback.innerHTML = '<i class="bi bi-check-circle"></i> ' + (res.message || 'Seat reserved successfully! Check WhatsApp & Email.');
                            feedback.style.display = 'block';
                        }
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="bi bi-check2"></i> Seat Confirmed';
                        }
                        setTimeout(function () {
                            closeModal();
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<span>Reserve Workshop Seat</span> <i class="bi bi-arrow-right"></i>';
                            }
                        }, 2200);
                    } else {
                        showError((res && res.message) ? res.message : 'Unable to reserve seat. Please try again.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<span>Reserve Workshop Seat</span> <i class="bi bi-arrow-right"></i>';
                        }
                    }
                }).catch(function () {
                    showError('Connection error. Please try again or reach out directly on WhatsApp.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span>Reserve Workshop Seat</span> <i class="bi bi-arrow-right"></i>';
                    }
                });
            });
        }
    }

    /* ================= BOOT ============================================ */
    ready(function () {
        wireContactForm();
        wireAuthModal();
        wireAuthExtras();
        ensureBrochureModal();
        wireBrochureTriggers();
        wireWorkshopModal();
        bindCourseWhatsAppCTA();
        deduplicateCourseGifs();

        // Sync header height to CSS property for sticky elements on mobile
        function syncHeaderHeight() {
            var header = document.querySelector(".header");
            if (header) {
                var h = Math.round(header.getBoundingClientRect().height);
                document.documentElement.style.setProperty("--tcm-header-height", h + "px");
            }
        }
        syncHeaderHeight();
        window.addEventListener("resize", syncHeaderHeight, { passive: true });
        window.addEventListener("orientationchange", syncHeaderHeight, { passive: true });

        // Center active tab smoothly on mobile when tapped
        document.querySelectorAll(".course-tabs .nav-link").forEach(function (btn) {
            btn.addEventListener("click", function () {
                var self = this;
                setTimeout(function () {
                    self.scrollIntoView({ behavior: "smooth", inline: "center", block: "nearest" });
                }, 50);
            });
        });

        // Ensure generic static brochure download links (outside course cards/detail) respect base path
        document.querySelectorAll('a[href="/download-brochure"]:not(.cd-brochure-btn):not(.ws-btn-brochure), a[href="download-brochure"]:not(.cd-brochure-btn):not(.ws-btn-brochure)').forEach(function (el) {
            el.setAttribute("href", api("/download-brochure"));
        });

        getJSON(api("/api/me"))
            .then(function (res) { if (res && res.data) ME = Object.assign(ME, res.data); })
            .catch(function () {})
            .finally(function () {
                applyAuthState();
                wireWhatsApp();
                hydrateLists();
                hydrateDetailPage();
                bindCourseWhatsAppCTA();
                deduplicateCourseGifs();
            });
    });
})();
