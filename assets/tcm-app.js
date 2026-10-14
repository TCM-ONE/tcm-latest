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
    function trackEvent(name, data) {
        try {
            if (typeof window.gtag !== "function") return;
            var payload = data || {};
            payload.page_location = window.location.href;
            window.gtag("event", name, payload);
        } catch (err) {}
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
        document.querySelectorAll(".join-btn:not(.demo-trigger)").forEach(function (el) {
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
                    if (res.success) {
                        trackEvent("contact_lead_created", {
                            subject: payload.subject || "contact",
                            method: "contact_form"
                        });
                        if ((payload.subject || "").toLowerCase().indexOf("course") !== -1) {
                            trackEvent("course_enquiry", {
                                source: "contact_form",
                                enquiry_type: payload.subject
                            });
                        }
                        form.reset();
                    }
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

    // index.html course card & distinct media mapping for all 24 courses
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
        "coding-analytics.gif",
        "coding-cloud.gif",
        "coding-cloud-new.gif",
        "coding-dsa.gif",
        "coding-terminal.gif",
        "coding.gif"
    ];

    var courseMediaMap = {
        "full-stack-web-development": "coding-fullstack.gif",
        "frontend-development-with-react": "coding-frontend.gif",
        "python-programming-automation": "coding-python.gif",
        "sql-database-fundamentals": "sql-database-fundamentals.svg",
        "dsa-problem-solving-java": "coding-dsa.gif",
        "aws-cloud-deployment-fundamentals": "coding-cloud.gif",
        "digital-skills-computer-fundamentals": "coding-basics.gif",
        "build-your-first-website": "coding-webdev.gif",
        "ai-tools-for-study-work": "coding-ai.gif",
        "tech-career-launchpad": "coding-career.gif",
        "ai-automation-nocode-tools": "coding-automation.gif",
        "data-analytics-excel-sql-powerbi": "coding-analytics.gif",
        "react-nextjs-frontend-engineering": "coding-monitor.gif",
        "backend-development-nodejs-apis": "coding-terminal.gif",
        "mobile-app-development-react-native": "react-native-app.svg",
        "generative-ai-agent-engineering": "genai-ai-agents.svg",
        "docker-devops-cicd-essentials": "coding-cloud-new.gif",
        "ui-ux-design-with-figma": "uiux-figma.svg",
        "software-testing-qa-playwright": "qa-playwright.svg",
        "digital-marketing-seo-analytics": "digital-marketing-seo.svg",
        "core-java-object-oriented-programming": "core-java-oop.svg",
        "data-science-machine-learning-foundations": "coding-algorithm.gif",
        "system-design-microservices-foundations": "coding-database.gif",
        "cybersecurity-ethical-security-foundations": "coding.gif",
        "artificial-intelligence-machine-learning": "ai-machine-learning.svg",
        "ai-machine-learning-fundamentals": "ai-machine-learning.svg",
        "prompt-engineering-generative-ai": "genai-ai-agents.svg",
        "python-for-ai-ml": "coding-python.gif",
        "ai-agents-workflow-automation": "coding-automation.gif",
        "chatgpt-gemini-ai-productivity-tools": "coding-ai.gif",
        "machine-learning-python-projects": "coding-algorithm.gif"
    };

    function getUniqueGifForCourse(slug, index) {
        if (slug && courseMediaMap[slug]) {
            return courseMediaMap[slug];
        }
        var fallbackIdx = (typeof index === 'number' ? index : 0) % allGifs.length;
        return allGifs[fallbackIdx];
    }

    function normalizeThumbnailUrl(url, slug, idx) {
        if (slug && courseMediaMap[slug]) {
            return api('/assets/' + courseMediaMap[slug]);
        }
        if (!url || typeof url !== 'string' || url.trim() === '') {
            return api('/assets/' + getUniqueGifForCourse(slug, idx));
        }
        var clean = url.trim();
        if (clean.startsWith('http://') || clean.startsWith('https://')) {
            return clean;
        }
        var filename = clean.split('/').pop().split('?')[0];
        if (clean.indexOf('uploads/') !== -1) {
            var uploadPath = clean.substring(clean.indexOf('uploads/'));
            return api('/' + uploadPath);
        }
        return api('/assets/' + filename);
    }

    var USD_EXCHANGE_RATE = 83.5;
    var activeCurrency = "inr";
    try {
        var savedCurr = localStorage.getItem("tcm_currency");
        if (savedCurr === "usd" || savedCurr === "inr") activeCurrency = savedCurr;
    } catch (e) {}

    function inrToUsd(amount) {
        var inr = Number(amount || 0);
        if (!inr || inr <= 0) return 0;
        return Math.round(inr / USD_EXCHANGE_RATE);
    }

    function renderCardPriceHtml(priceInr, origInr) {
        var pInr = Number(priceInr || 0);
        var pUsd = inrToUsd(pInr);
        var oInr = origInr ? Number(origInr) : 0;
        var oUsd = oInr ? inrToUsd(oInr) : 0;

        if (activeCurrency === "usd") {
            return '<strong class="price-val">$' + pUsd.toLocaleString("en-US") + ' USD</strong>' +
                ' <span class="course-price-inr-tag">(₹' + pInr.toLocaleString("en-IN") + ')</span>' +
                (oUsd ? ' <span class="course-price-orig">$' + oUsd.toLocaleString("en-US") + '</span>' : '');
        }
        return '<strong class="price-val">₹' + pInr.toLocaleString("en-IN") + '</strong>' +
            ' <span class="course-price-usd-tag">($' + pUsd.toLocaleString("en-US") + ' USD)</span>' +
            (oInr ? ' <span class="course-price-orig">₹' + oInr.toLocaleString("en-IN") + '</span>' : '');
    }

    function courseCardHome(c, idx) {
        var priceInr = Number(c.price || 0);
        var origInr = c.original_price ? Number(c.original_price) : 0;
        var price = '<div class="course-price">' + renderCardPriceHtml(priceInr, origInr) + '</div>';
        var thumbSrc = normalizeThumbnailUrl(c.thumbnail_url || c.thumbnail, c.slug, idx);
        var thumb = '<div class="course-card-thumb"><img src="' + esc(thumbSrc) + '" alt="' + esc(c.title) + '" data-course-slug="' + esc(c.slug) + '" loading="lazy" decoding="async" onerror="this.onerror=null;this.src=\'' + esc(api('/assets/coding-fullstack.gif')) + '\';"></div>';
        var track = inferCourseTrack(c.slug, c.title, c.category_name || c.category || "");
        var brochureUrl = c.brochure_url || api('/courses/' + encodeURIComponent(c.slug || '') + '/brochure');
        return '<div class="course-card" data-course-slug="' + esc(c.slug) + '" data-course-title="' + esc(c.title) + '" data-course-track="' + esc(track) + '" data-course-price="' + priceInr + '"' + (origInr ? ' data-course-orig-price="' + origInr + '"' : '') + '>' +
            thumb +
            price +
            "<h3>" + esc(c.title) + "</h3>" +
            "<p>" + esc(c.subtitle || "") + "</p>" +
            '<div class="course-card-actions">' +
                '<a href="' + api('/courses/' + encodeURIComponent(c.slug)) + '" class="btn-roadmap">Get Road Map <i class="bi bi-arrow-right"></i></a>' +
                '<a href="' + esc(brochureUrl) + '" class="btn-brochure btn-trigger-brochure" data-course-slug="' + esc(c.slug) + '" data-course-title="' + esc(c.title) + '" data-download-url="' + esc(brochureUrl) + '"><i class="bi bi-file-earmark-pdf"></i> Course PDF</a>' +
            '</div>' +
            "</div>";
    }

    var courseTrackLabels = {
        all: "All learning tracks",
        web: "Web & App Development",
        ai: "AI, ML & Automation",
        data: "Data & Analytics",
        cloud: "Cloud, DevOps & Security",
        career: "Career, DSA & Foundations",
        design: "Design, QA & Marketing"
    };
    var activeCourseTrack = "all";

    var coursePriceLabels = {
        all: "All Pricing",
        "under-2k": "Under ₹2,000 (<$25)",
        "2k-5k": "₹2,000 – ₹5,000 ($25–$60)",
        "5k-10k": "₹5,000 – ₹10,000 ($60–$120)",
        "above-10k": "Above ₹10,000 ($120+)"
    };
    var activeCoursePrice = "all";

    function matchPriceFilter(price, filter) {
        if (!filter || filter === "all") return true;
        var p = Number(price || 0);
        if (filter === "under-2k") return p > 0 && p < 2000;
        if (filter === "2k-5k") return p >= 2000 && p <= 5000;
        if (filter === "5k-10k") return p > 5000 && p <= 10000;
        if (filter === "above-10k") return p > 10000;
        return true;
    }

    function inferCourseTrack(slug, title, category) {
        var hay = ((slug || "") + " " + (title || "") + " " + (category || "")).toLowerCase();
        if (/ui|ux|figma|testing|qa|playwright|marketing|seo/.test(hay)) return "design";
        if (/cloud|aws|devops|docker|cicd|security|cyber/.test(hay)) return "cloud";
        if (/data|analytics|sql|powerbi|database/.test(hay)) return "data";
        if (/ai|machine|learning|automation|genai|generative|agent|prompt|openai|llm/.test(hay)) return "ai";
        if (/full-stack|frontend|backend|react|node|mobile|website|web/.test(hay)) return "web";
        if (/dsa|career|java|python|digital|computer|foundation|fundamental/.test(hay)) return "career";
        return "web";
    }

    function shuffleArray(items) {
        for (var i = items.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = items[i];
            items[i] = items[j];
            items[j] = tmp;
        }
        return items;
    }

    function shuffleStaticCourseCards() {
        document.querySelectorAll(".courses-section .course-grid").forEach(function (grid) {
            var cards = Array.prototype.slice.call(grid.querySelectorAll(":scope > .course-card"));
            if (cards.length < 2) return;
            shuffleArray(cards).forEach(function (card) { grid.appendChild(card); });
        });
    }

    function decorateCourseCards() {
        document.querySelectorAll(".courses-section .course-card").forEach(function (card) {
            if (!card.getAttribute("data-course-track")) {
                var slug = card.getAttribute("data-course-slug") || "";
                var title = card.getAttribute("data-course-title") || (card.querySelector("h3") ? card.querySelector("h3").textContent : "");
                card.setAttribute("data-course-track", inferCourseTrack(slug, title, ""));
            }
            if (!card.getAttribute("data-course-price")) {
                var priceEl = card.querySelector(".course-price");
                if (priceEl) {
                    var match = priceEl.textContent.match(/[\d,]+/);
                    if (match) {
                        card.setAttribute("data-course-price", match[0].replace(/,/g, ""));
                    }
                }
            }
            if (!card.getAttribute("data-course-orig-price")) {
                var pEl = card.querySelector(".course-price");
                if (pEl) {
                    var origSpan = pEl.querySelector("span.course-price-orig, del, s, span:not(.course-price-usd-tag):not(.course-price-inr-tag):not(.price-val)");
                    if (origSpan) {
                        var m = origSpan.textContent.match(/[\d,]+/);
                        if (m) card.setAttribute("data-course-orig-price", m[0].replace(/,/g, ""));
                    }
                }
            }
        });
    }

    function updateCourseCardPrices() {
        document.querySelectorAll(".courses-section .course-card").forEach(function (card) {
            var rawPrice = card.getAttribute("data-course-price");
            if (!rawPrice) return;
            var priceInr = parseInt(rawPrice, 10);
            if (!priceInr || isNaN(priceInr)) return;

            var origInr = card.getAttribute("data-course-orig-price");
            var numOrigInr = origInr ? parseInt(origInr, 10) : 0;

            var priceEl = card.querySelector(".course-price");
            if (priceEl) {
                priceEl.innerHTML = renderCardPriceHtml(priceInr, numOrigInr);
            }
        });
    }

    function setCurrency(curr) {
        activeCurrency = (curr === "usd") ? "usd" : "inr";
        try {
            localStorage.setItem("tcm_currency", activeCurrency);
        } catch (e) {}
        document.querySelectorAll(".course-currency-btn").forEach(function (btn) {
            btn.classList.toggle("active", btn.getAttribute("data-currency") === activeCurrency);
        });
        updateCourseCardPrices();
    }

    function ensureCourseEmptyState(grid) {
        var empty = grid.querySelector(":scope > .course-grid-empty");
        if (!empty) {
            empty = document.createElement("div");
            empty.className = "course-grid-empty";
            empty.textContent = "No courses match the selected filters in this tab.";
            grid.appendChild(empty);
        }
        return empty;
    }

    function activeCoursePane() {
        return document.querySelector(".courses-section .tab-pane.active.show") ||
            document.querySelector(".courses-section .tab-pane.active") ||
            document.querySelector(".courses-section");
    }

    function applyCourseFilters() {
        decorateCourseCards();
        var pane = activeCoursePane();
        var activeVisible = 0;
        document.querySelectorAll(".courses-section .course-grid").forEach(function (grid) {
            var visibleInGrid = 0;
            grid.querySelectorAll(":scope > .course-card").forEach(function (card) {
                var track = card.getAttribute("data-course-track") || "web";
                var price = card.getAttribute("data-course-price") || 0;
                var trackMatch = activeCourseTrack === "all" || track === activeCourseTrack;
                var priceMatch = matchPriceFilter(price, activeCoursePrice);
                var show = trackMatch && priceMatch;
                card.classList.toggle("course-filter-hidden", !show);
                if (show) {
                    visibleInGrid++;
                    if (pane && pane.contains(card)) activeVisible++;
                }
            });
            ensureCourseEmptyState(grid).classList.toggle("visible", visibleInGrid === 0);
        });

        var summary = document.getElementById("courseFilterSummary");
        var count = document.getElementById("courseFilterCount");
        if (summary) {
            var tText = courseTrackLabels[activeCourseTrack] || courseTrackLabels.all;
            var pText = coursePriceLabels[activeCoursePrice] || coursePriceLabels.all;
            if (activeCourseTrack === "all" && activeCoursePrice === "all") {
                summary.textContent = "All learning tracks · All pricing";
            } else if (activeCourseTrack !== "all" && activeCoursePrice !== "all") {
                summary.textContent = tText + " · " + pText;
            } else if (activeCourseTrack !== "all") {
                summary.textContent = tText;
            } else {
                summary.textContent = pText;
            }
        }
        if (count) count.textContent = activeVisible ? activeVisible + " visible" : "No match";
    }

    function wireCourseFunnel() {
        var allDropdowns = document.querySelectorAll(".course-filter-dropdown");
        allDropdowns.forEach(function (dropdown) {
            var toggle = dropdown.querySelector(".course-filter-toggle");
            if (!toggle) return;
            toggle.addEventListener("click", function (e) {
                e.stopPropagation();
                var open = !dropdown.classList.contains("open");
                allDropdowns.forEach(function (other) {
                    if (other !== dropdown) {
                        other.classList.remove("open");
                        var ot = other.querySelector(".course-filter-toggle");
                        if (ot) ot.setAttribute("aria-expanded", "false");
                    }
                });
                dropdown.classList.toggle("open", open);
                toggle.setAttribute("aria-expanded", open ? "true" : "false");
            });
        });

        // Track filter menu options
        var trackMenu = document.getElementById("courseFilterMenu");
        var trackToggle = document.getElementById("courseFilterToggle");
        if (trackMenu) {
            trackMenu.querySelectorAll(".course-filter-option").forEach(function (option) {
                option.addEventListener("click", function () {
                    activeCourseTrack = option.getAttribute("data-filter") || "all";
                    trackMenu.querySelectorAll(".course-filter-option").forEach(function (btn) {
                        btn.classList.toggle("active", btn === option);
                    });
                    if (trackToggle) {
                        var label = trackToggle.querySelector("span");
                        if (label) label.textContent = courseTrackLabels[activeCourseTrack] || "Choose Track";
                        trackToggle.setAttribute("aria-expanded", "false");
                    }
                    var dd = trackMenu.closest(".course-filter-dropdown");
                    if (dd) dd.classList.remove("open");
                    applyCourseFilters();
                });
            });
        }

        // Price filter menu options
        var priceMenu = document.getElementById("coursePriceMenu");
        var priceToggle = document.getElementById("coursePriceToggle");
        if (priceMenu) {
            priceMenu.querySelectorAll(".course-filter-option").forEach(function (option) {
                option.addEventListener("click", function () {
                    activeCoursePrice = option.getAttribute("data-price-filter") || "all";
                    priceMenu.querySelectorAll(".course-filter-option").forEach(function (btn) {
                        btn.classList.toggle("active", btn === option);
                    });
                    if (priceToggle) {
                        var label = priceToggle.querySelector("span");
                        if (label) label.textContent = coursePriceLabels[activeCoursePrice] || "All Pricing";
                        priceToggle.setAttribute("aria-expanded", "false");
                    }
                    var dd = priceMenu.closest(".course-filter-dropdown");
                    if (dd) dd.classList.remove("open");
                    applyCourseFilters();
                });
            });
        }

        // Currency Switcher buttons
        document.querySelectorAll(".course-currency-btn").forEach(function (btn) {
            btn.addEventListener("click", function () {
                var curr = btn.getAttribute("data-currency") || "inr";
                setCurrency(curr);
            });
        });

        document.addEventListener("click", function (e) {
            allDropdowns.forEach(function (dropdown) {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove("open");
                    var t = dropdown.querySelector(".course-filter-toggle");
                    if (t) t.setAttribute("aria-expanded", "false");
                }
            });
        });

        document.querySelectorAll('.course-tabs [data-bs-toggle="pill"]').forEach(function (tab) {
            tab.addEventListener("shown.bs.tab", applyCourseFilters);
            tab.addEventListener("click", function () { setTimeout(applyCourseFilters, 80); });
        });

        // Initialize currency button states
        document.querySelectorAll(".course-currency-btn").forEach(function (btn) {
            btn.classList.toggle("active", btn.getAttribute("data-currency") === activeCurrency);
        });

        decorateCourseCards();
        updateCourseCardPrices();
        applyCourseFilters();
    }

    function wireMobileLanguagePicker() {
        var labels = {
            en: "EN",
            hi: "HI",
            mr: "MR",
            gu: "GU",
            bn: "BN",
            ta: "TA",
            te: "TE",
            kn: "KN",
            ml: "ML",
            pa: "PA",
            ur: "UR",
            ar: "AR",
            fr: "FR",
            es: "ES",
            de: "DE",
            it: "IT",
            pt: "PT",
            ru: "RU",
            ko: "KO",
            "zh-CN": "ZH",
            ja: "JA",
            ne: "NE",
            th: "TH",
            vi: "VI"
        };
        var saved = "en";
        try { saved = localStorage.getItem("tcm_site_lang") || "en"; } catch (e) {}

        function syncPickers(lang) {
            document.querySelectorAll(".mobile-lang-picker").forEach(function (picker) {
                var label = picker.querySelector(".mobile-lang-code");
                if (label) label.textContent = labels[lang] || "EN";
                picker.querySelectorAll("[data-lang]").forEach(function (btn) {
                    btn.classList.toggle("active", btn.getAttribute("data-lang") === lang);
                });
            });
        }

        syncPickers(saved);
        document.querySelectorAll(".mobile-lang-picker").forEach(function (picker) {
            var toggle = picker.querySelector(".mobile-lang-toggle");
            if (!toggle || picker.getAttribute("data-lang-wired") === "true") return;
            picker.setAttribute("data-lang-wired", "true");
            toggle.addEventListener("click", function (e) {
                e.stopPropagation();
                var open = !picker.classList.contains("open");
                document.querySelectorAll(".mobile-lang-picker.open").forEach(function (p) {
                    if (p !== picker) p.classList.remove("open");
                });
                picker.classList.toggle("open", open);
                toggle.setAttribute("aria-expanded", open ? "true" : "false");
            });
            picker.querySelectorAll("[data-lang]").forEach(function (btn) {
                btn.addEventListener("click", function () {
                    var lang = btn.getAttribute("data-lang") || "en";
                    try { localStorage.setItem("tcm_site_lang", lang); } catch (e) {}
                    syncPickers(lang);
                    document.querySelectorAll(".mobile-lang-picker.open").forEach(function (p) { p.classList.remove("open"); });
                    document.querySelectorAll(".mobile-lang-toggle").forEach(function (t) { t.setAttribute("aria-expanded", "false"); });
                    applySiteTranslation(lang);
                });
            });
        });
        document.addEventListener("tcm:translate-ready", function () {
            if (saved !== "en") applySiteTranslation(saved);
        });
        document.addEventListener("click", function (e) {
            if (e.target.closest(".mobile-lang-picker")) return;
            document.querySelectorAll(".mobile-lang-picker.open").forEach(function (picker) {
                picker.classList.remove("open");
            });
            document.querySelectorAll(".mobile-lang-toggle").forEach(function (toggle) {
                toggle.setAttribute("aria-expanded", "false");
            });
        });
    }

    function setTranslateCookie(lang) {
        var target = lang === "en" ? "/en/en" : "/en/" + lang;
        var expires = "expires=Fri, 31 Dec 9999 23:59:59 GMT";
        document.cookie = "googtrans=" + target + "; " + expires + "; path=/";
        if (location.hostname.indexOf(".") !== -1) {
            document.cookie = "googtrans=" + target + "; " + expires + "; path=/; domain=." + location.hostname.replace(/^www\./, "");
        }
    }

    function applySiteTranslation(lang, attempt) {
        attempt = attempt || 0;
        document.documentElement.setAttribute("lang", lang === "en" ? "en-IN" : lang);
        setTranslateCookie(lang);
        var combo = document.querySelector(".goog-te-combo");
        if (!combo) {
            if (attempt < 20) {
                setTimeout(function () { applySiteTranslation(lang, attempt + 1); }, 250);
            }
            return;
        }
        combo.value = lang === "en" ? "" : lang;
        combo.dispatchEvent(new Event("change"));
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
            if (type === "courses") {
                items = shuffleArray(items.slice());
            }
            var html = items.map(function (item, i) {
                if (type === "courses") return courseCardHome(item, i);
                if (type === "posts")   return postCard(item);
                return tpl === "pg" ? eventCardPg(item) : eventCardHome(item);
            }).join("");
            container.innerHTML = html;
            if (type === "courses") {
                decorateCourseCards();
                updateCourseCardPrices();
                applyCourseFilters();
                normalizeBrochureTriggers();
            }

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

    function setHeroStat(key, value) {
        var node = document.querySelector('[data-hero-stat="' + key + '"] [data-stat-value]');
        if (node && value) node.textContent = value;
    }

    function formatPlusCount(count) {
        var n = parseInt(count, 10) || 0;
        if (n >= 100) return Math.floor(n / 10) * 10 + "+";
        if (n >= 10) return n + "+";
        return Math.max(n, 10) + "+";
    }

    function countStaticCourseCards() {
        var slugs = {};
        document.querySelectorAll(".courses-section .course-card[data-course-slug]").forEach(function (card) {
            var slug = card.getAttribute("data-course-slug");
            if (slug) slugs[slug] = true;
        });
        return Object.keys(slugs).length;
    }

    function updateHeroStats(courseCount) {
        var fallback = countStaticCourseCards();
        setHeroStat("courses", formatPlusCount(courseCount || fallback || 10));
        setHeroStat("classes", "Live");
        setHeroStat("support", "24/7");
        setHeroStat("practical", "100%");
    }

    function hydrateHeroStats() {
        updateHeroStats();
        getJSON(api("/api/courses"))
            .then(function (res) {
                if (res && res.success && Array.isArray(res.data)) {
                    updateHeroStats(res.data.length);
                }
            })
            .catch(function () {});
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
                this.src = api('/assets/coding-fullstack.gif');
            };
            if (!img.getAttribute('loading')) img.setAttribute('loading', 'lazy');
            if (!img.getAttribute('decoding')) img.setAttribute('decoding', 'async');
            var currentSrc = img.getAttribute('src') || '';
            if (!currentSrc || currentSrc.trim() === '' || (!currentSrc.includes('.gif') && !currentSrc.includes('.png') && !currentSrc.includes('.jpg') && !currentSrc.includes('.webp') && !currentSrc.includes('.svg'))) {
                var slug = img.getAttribute('data-course-slug') || '';
                img.src = api('/assets/' + getUniqueGifForCourse(slug, idx));
            }
        });
    }

    function brochureUrlForSlug(slug) {
        return slug
            ? api('/courses/' + encodeURIComponent(slug) + '/brochure')
            : api('/download-brochure');
    }

    function normalizeBrochureTriggers() {
        document.querySelectorAll('.btn-trigger-brochure, .cd-brochure-btn, .ws-btn-brochure, [data-trigger-brochure]').forEach(function (trigger) {
            var slug = trigger.getAttribute('data-course-slug') || '';
            if (!slug) {
                var card = trigger.closest('.course-card');
                if (card) slug = card.getAttribute('data-course-slug') || '';
            }
            var url = trigger.getAttribute('data-download-url') || brochureUrlForSlug(slug);
            trigger.setAttribute('data-download-url', url);
            if (trigger.tagName === 'A') {
                trigger.setAttribute('href', url);
            }
        });
    }

    /* ================= FREE DEMO MODAL ================================ */
    function ensureDemoModal() {
        if (document.getElementById("tcmDemoModalOverlay")) return;

        var overlay = document.createElement("div");
        overlay.id = "tcmDemoModalOverlay";
        overlay.className = "tcm-demo-modal-overlay";
        overlay.innerHTML =
            '<div class="tcm-demo-modal" role="dialog" aria-modal="true" aria-labelledby="tcmDemoModalTitle">' +
                '<button type="button" class="tcm-demo-close" id="tcmDemoCloseBtn" aria-label="Close demo form">&times;</button>' +
                '<div class="tcm-demo-cover">' +
                    '<img src="' + esc(api('/assets/demo-modal-hero.gif')) + '" alt="Book a Free Live Demo Class" loading="eager">' +
                    '<div class="tcm-demo-cover-copy">' +
                        '<span>Live 1-on-1 Preview</span>' +
                        '<h3>Book Your Free Demo</h3>' +
                        '<p>Join a live interactive walkthrough with senior tech mentors &amp; personalized career roadmap.</p>' +
                    '</div>' +
                '</div>' +
                '<div class="tcm-demo-panel">' +
                    '<div class="tcm-demo-form-state" id="tcmDemoFormState">' +
                        '<div class="tcm-demo-kicker">Free Demo Session</div>' +
                        '<h3 id="tcmDemoModalTitle">Get a Free Live Demo</h3>' +
                        '<p>Choose a course below and our admissions mentor will schedule your 1-on-1 live demo slot.</p>' +
                        '<form id="tcmDemoLeadForm" novalidate>' +
                            '<div class="tcm-demo-grid">' +
                                '<div class="form-group">' +
                                    '<label for="tcmDemoName">Full Name *</label>' +
                                    '<input type="text" id="tcmDemoName" class="form-control" name="name" placeholder="e.g. Rahul Sharma" required>' +
                                '</div>' +
                                '<div class="form-group">' +
                                    '<label for="tcmDemoPhone">WhatsApp / Mobile *</label>' +
                                    '<input type="tel" id="tcmDemoPhone" class="form-control" name="phone" placeholder="e.g. 9876543210" maxlength="14" required>' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label for="tcmDemoEmail">Email Address *</label>' +
                                '<input type="email" id="tcmDemoEmail" class="form-control" name="email" placeholder="e.g. rahul@example.com" required>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label for="tcmDemoCourse">Select Course *</label>' +
                                '<select id="tcmDemoCourse" class="form-control" name="course_slug" required>' +
                                    '<option value="">Loading courses...</option>' +
                                '</select>' +
                            '</div>' +
                            '<div class="tcm-demo-grid">' +
                                '<div class="form-group">' +
                                    '<label for="tcmDemoSlot">Preferred Slot</label>' +
                                    '<select id="tcmDemoSlot" class="form-control" name="preferred_slot">' +
                                        '<option value="Evening 6 PM - 9 PM">Evening 6 PM - 9 PM</option>' +
                                        '<option value="Morning 9 AM - 12 PM">Morning 9 AM - 12 PM</option>' +
                                        '<option value="Weekend">Weekend</option>' +
                                        '<option value="Any time">Any time</option>' +
                                    '</select>' +
                                '</div>' +
                                '<div class="form-group">' +
                                    '<label for="tcmDemoGoal">Career Goal</label>' +
                                    '<input type="text" id="tcmDemoGoal" class="form-control" name="goal" placeholder="Job, switch, college, project">' +
                                '</div>' +
                            '</div>' +
                            '<div id="tcmDemoFeedback" class="tcm-demo-feedback"></div>' +
                            '<button type="submit" class="tcm-demo-submit" id="tcmDemoSubmitBtn">' +
                                '<span>Request Free Demo</span> <i class="bi bi-arrow-right"></i>' +
                            '</button>' +
                            '<div style="text-align:center;font-size:11.5px;color:#777;margin-top:8px;">' +
                                '<i class="bi bi-shield-check"></i> 100% free &middot; Instant confirmation on WhatsApp' +
                            '</div>' +
                        '</form>' +
                    '</div>' +
                    '<div class="tcm-demo-success-state" id="tcmDemoSuccessState" aria-live="polite">' +
                        '<div class="tcm-demo-success-icon"><i class="bi bi-check2"></i></div>' +
                        '<h3>Demo Request Received!</h3>' +
                        '<p>Our mentors will contact you shortly with your private demo session link and slot details.</p>' +
                        '<button type="button" class="tcm-demo-submit" id="tcmDemoDoneBtn">Done</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        document.body.appendChild(overlay);

        var closeBtn = document.getElementById("tcmDemoCloseBtn");
        if (closeBtn) closeBtn.addEventListener("click", closeDemoModal);
        var doneBtn = document.getElementById("tcmDemoDoneBtn");
        if (doneBtn) doneBtn.addEventListener("click", closeDemoModal);
        overlay.addEventListener("click", function (e) {
            if (e.target === overlay) closeDemoModal();
        });
        var form = document.getElementById("tcmDemoLeadForm");
        if (form) form.addEventListener("submit", handleDemoFormSubmit);
    }

    function openDemoModal(prefill) {
        ensureDemoModal();
        var targetSlug = (prefill && prefill.course_slug) ? prefill.course_slug : null;
        loadDemoCourses(targetSlug);
        var overlay = document.getElementById("tcmDemoModalOverlay");
        var formState = document.getElementById("tcmDemoFormState");
        var successState = document.getElementById("tcmDemoSuccessState");
        var feedback = document.getElementById("tcmDemoFeedback");
        if (formState) formState.style.display = "block";
        if (successState) successState.style.display = "none";
        if (feedback) {
            feedback.className = "tcm-demo-feedback";
            feedback.textContent = "";
            feedback.style.display = "none";
        }

        var nameInput = document.getElementById("tcmDemoName");
        var emailInput = document.getElementById("tcmDemoEmail");
        var phoneInput = document.getElementById("tcmDemoPhone");
        var courseSelect = document.getElementById("tcmDemoCourse");
        if (ME && ME.authenticated) {
            if (nameInput && !nameInput.value) nameInput.value = ME.name || "";
            if (emailInput && !emailInput.value) emailInput.value = ME.email || "";
        }
        try {
            var savedName = localStorage.getItem("tcm_lead_name");
            var savedEmail = localStorage.getItem("tcm_lead_email");
            var savedPhone = localStorage.getItem("tcm_lead_phone");
            if (savedName && nameInput && !nameInput.value) nameInput.value = savedName;
            if (savedEmail && emailInput && !emailInput.value) emailInput.value = savedEmail;
            if (savedPhone && phoneInput && !phoneInput.value) phoneInput.value = savedPhone;
        } catch (e) {}
        if (prefill) {
            if (prefill.email && emailInput && !emailInput.value) emailInput.value = prefill.email;
            if (prefill.phone && phoneInput && !phoneInput.value) phoneInput.value = prefill.phone;
            if (prefill.course_slug && courseSelect) courseSelect.value = prefill.course_slug;
        }
        if (overlay) {
            overlay.classList.add("active");
            document.body.style.overflow = "hidden";
            setTimeout(function () {
                var first = nameInput && !nameInput.value ? nameInput : phoneInput;
                if (first) first.focus();
            }, 120);
        }
    }

    function closeDemoModal() {
        var overlay = document.getElementById("tcmDemoModalOverlay");
        if (overlay) overlay.classList.remove("active");
        document.body.style.overflow = "";
    }

    function loadDemoCourses(preselectedSlug) {
        var select = document.getElementById("tcmDemoCourse");
        if (!select) return;

        // Immediate fallback from existing cards in the DOM so select is never empty
        var pageCourses = [];
        var seen = {};
        document.querySelectorAll(".course-card").forEach(function (card) {
            var slug = card.getAttribute("data-course-slug");
            var title = card.getAttribute("data-course-title") || (card.querySelector("h3") ? card.querySelector("h3").textContent.trim() : "");
            var price = card.getAttribute("data-course-price") || "";
            if (slug && title && !seen[slug]) {
                seen[slug] = true;
                pageCourses.push({ slug: slug, title: title, price: price });
            }
        });

        function renderOptions(courses) {
            if (!courses || !courses.length) {
                select.innerHTML = '<option value="">-- Choose a Course for Free Demo --</option>';
                return;
            }
            var html = '<option value="">-- Choose a Course for Free Demo --</option>';
            courses.forEach(function (c) {
                var pInr = Number(c.price || 0);
                var pUsd = inrToUsd(pInr);
                var priceStr = pInr ? (' - ₹' + pInr.toLocaleString('en-IN') + ' ($' + pUsd + ' USD)') : '';
                html += '<option value="' + esc(c.slug) + '">' + esc(c.title) + priceStr + '</option>';
            });
            select.innerHTML = html;
            if (preselectedSlug) {
                select.value = preselectedSlug;
            }
            select.setAttribute("data-loaded", "true");
        }

        // Render page cards immediately if not yet loaded from API
        if (select.getAttribute("data-loaded") !== "true" && pageCourses.length > 0) {
            renderOptions(pageCourses);
        }

        // Fetch authoritative course list from API
        getJSON(api("/api/courses"))
            .then(function (res) {
                var courses = (res && res.success && Array.isArray(res.data)) ? res.data : [];
                if (courses.length > 0) {
                    renderOptions(courses);
                } else if (pageCourses.length > 0) {
                    renderOptions(pageCourses);
                }
            })
            .catch(function (err) {
                console.warn("[TCM Demo] Course API fetch error, using page cards:", err);
                if (pageCourses.length > 0) {
                    renderOptions(pageCourses);
                } else {
                    select.innerHTML = '<option value="">-- Choose a Course for Free Demo --</option>';
                }
            });
    }

    function handleDemoFormSubmit(e) {
        e.preventDefault();
        var feedback = document.getElementById("tcmDemoFeedback");
        var submitBtn = document.getElementById("tcmDemoSubmitBtn");
        var nameInput = document.getElementById("tcmDemoName");
        var emailInput = document.getElementById("tcmDemoEmail");
        var phoneInput = document.getElementById("tcmDemoPhone");
        var courseInput = document.getElementById("tcmDemoCourse");
        var slotInput = document.getElementById("tcmDemoSlot");
        var goalInput = document.getElementById("tcmDemoGoal");

        var payload = {
            name: nameInput ? nameInput.value.trim() : "",
            email: emailInput ? emailInput.value.trim() : "",
            phone: phoneInput ? phoneInput.value.trim() : "",
            course_slug: courseInput ? courseInput.value.trim() : "",
            preferred_slot: slotInput ? slotInput.value.trim() : "",
            goal: goalInput ? goalInput.value.trim() : ""
        };

        function showDemoError(msg) {
            if (!feedback) return;
            feedback.className = "tcm-demo-feedback error";
            feedback.textContent = msg;
            feedback.style.display = "block";
        }

        if (!payload.name) return showDemoError("Please enter your full name.");
        if (!payload.email || !payload.email.includes("@")) return showDemoError("Please enter a valid email.");
        if (payload.phone.replace(/[^\d]/g, "").length < 10) return showDemoError("Please enter a valid mobile number.");
        if (!payload.course_slug) return showDemoError("Please choose a premium course.");

        try {
            localStorage.setItem("tcm_lead_name", payload.name);
            localStorage.setItem("tcm_lead_email", payload.email);
            localStorage.setItem("tcm_lead_phone", payload.phone);
        } catch (err) {}

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width:14px;height:14px;border-width:2px;"></span> Sending...';
        }
        postJSON(api("/api/demo-lead"), payload).then(function (res) {
            if (res && res.success) {
                trackEvent("generate_lead", { source: "free_demo_modal", course_slug: payload.course_slug });
                var formState = document.getElementById("tcmDemoFormState");
                var successState = document.getElementById("tcmDemoSuccessState");
                if (formState) formState.style.display = "none";
                if (successState) successState.style.display = "grid";
                if (e.target && e.target.reset) e.target.reset();
            } else {
                showDemoError((res && res.message) ? res.message : "Unable to submit demo request.");
            }
        }).catch(function () {
            showDemoError("Network error. Please try again.");
        }).finally(function () {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Request Demo <i class="bi bi-arrow-right"></i>';
            }
        });
    }

    function wireDemoTriggers() {
        document.addEventListener("click", function (e) {
            var trigger = e.target.closest(".demo-trigger");
            if (!trigger) return;
            e.preventDefault();
            e.stopPropagation();
            var prefill = {};
            var box = trigger.closest(".hero-input-box");
            var heroInput = box ? box.querySelector("input") : null;
            var val = heroInput ? heroInput.value.trim() : "";
            if (val) {
                if (val.indexOf("@") !== -1) prefill.email = val;
                else prefill.phone = val;
            }
            openDemoModal(prefill);
        }, true);
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
                trackEvent("course_enquiry", {
                    course_slug: slug,
                    source: "brochure_download",
                    method: "lead_form"
                });
                if (feedback) {
                    feedback.className = 'tcm-brochure-modal-feedback success';
                    feedback.innerHTML = '<i class="bi bi-check-circle"></i> Success! Starting brochure download now... ' +
                        '<a href="' + esc(res.data.download_url) + '" target="_blank" rel="noopener" download="' + esc((slug || 'TCM-One-Course') + '-Brochure.pdf') + '">Open PDF</a>';
                    feedback.style.display = 'block';
                }

                // Trigger file download via invisible link
                var downloadLink = document.createElement('a');
                downloadLink.href = res.data.download_url;
                downloadLink.setAttribute('download', (slug || 'TCM-One-Course') + '-Brochure.pdf');
                downloadLink.target = '_blank';
                document.body.appendChild(downloadLink);
                downloadLink.click();
                setTimeout(function () {
                    window.open(res.data.download_url, '_blank', 'noopener');
                }, 250);
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
        // Global click delegation for all brochure triggers across any cards, nav, hero, or detail pages
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('.btn-trigger-brochure, .cd-brochure-btn, .ws-btn-brochure, a[href*="download-brochure"], [data-trigger-brochure]');
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
    }

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
            trackEvent("workshop_registration_started", {
                workshop_slug: slug,
                workshop_id: id,
                workshop_title: title,
                value: Number(price || 0),
                currency: "INR"
            });
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
                        trackEvent("workshop_lead_created", {
                            workshop_slug: slugInput ? slugInput.value : '',
                            workshop_id: idInput ? idInput.value : '',
                            source: "workshop_registration_form"
                        });
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
        normalizeBrochureTriggers();
        ensureDemoModal();
        wireDemoTriggers();
        wireWorkshopModal();
        bindCourseWhatsAppCTA();
        deduplicateCourseGifs();
        wireMobileLanguagePicker();
        wireCourseFunnel();
        shuffleStaticCourseCards();
        decorateCourseCards();
        updateCourseCardPrices();
        applyCourseFilters();
        hydrateHeroStats();

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
                decorateCourseCards();
                updateCourseCardPrices();
                applyCourseFilters();
                updateHeroStats();
                normalizeBrochureTriggers();
            });
    });
})();
