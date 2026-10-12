/**
 * TCM One — Reusable ShareButton & Deep Linking Module
 * Supports Web Share API, WhatsApp, Clipboard copy with confirmation,
 * ad attribution parameter preservation, and sharp-corner Intercom UI.
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.TCMShare = factory();
    }
})(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    var allowedAdParams = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'fbclid',
        'igshid',
        'gclid',
        'ref'
    ];

    /**
     * Clean and preserve advertising attribution while stripping private student params.
     */
    function buildCleanShareUrl(rawUrl) {
        var base;
        try {
            base = new URL(rawUrl || window.location.href, window.location.origin);
        } catch (e) {
            return rawUrl || window.location.href;
        }

        // Current page params
        var currentParams = new URLSearchParams(window.location.search);
        var targetParams  = new URLSearchParams(base.search);

        // Inherit ad params from current visitor session if not already in target
        allowedAdParams.forEach(function (param) {
            if (currentParams.has(param) && !targetParams.has(param)) {
                targetParams.set(param, currentParams.get(param));
            }
        });

        // Strip any personal/student parameters
        var forbiddenParams = ['user_id', 'student_id', 'email', 'token', 'session', 'auth', 'otp', 'code'];
        forbiddenParams.forEach(function (p) {
            targetParams.delete(p);
        });

        var query = targetParams.toString();
        return base.origin + base.pathname + (query ? '?' + query : '');
    }

    // Modal singleton elements
    var overlayEl = null;
    var modalEl = null;
    var toastEl = null;
    var currentShareData = { title: '', text: '', url: '' };

    function createToastElement() {
        if (toastEl) return toastEl;
        toastEl = document.createElement('div');
        toastEl.className = 'tcm-share-toast';
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'polite');
        toastEl.innerHTML = '<i class="bi bi-check2-circle"></i> <span class="tcm-toast-text">Link Copied</span>';
        document.body.appendChild(toastEl);
        return toastEl;
    }

    function showToast(message) {
        var el = createToastElement();
        var txt = el.querySelector('.tcm-toast-text');
        if (txt) txt.textContent = message || 'Link Copied';
        el.classList.add('show');
        clearTimeout(el._hideTimeout);
        el._hideTimeout = setTimeout(function () {
            el.classList.remove('show');
        }, 2400);
    }

    function createModalElements() {
        if (overlayEl) return;

        overlayEl = document.createElement('div');
        overlayEl.className = 'tcm-share-overlay';
        overlayEl.setAttribute('aria-hidden', 'true');

        modalEl = document.createElement('div');
        modalEl.className = 'tcm-share-modal';
        modalEl.setAttribute('role', 'dialog');
        modalEl.setAttribute('aria-labelledby', 'tcmShareTitle');

        modalEl.innerHTML = [
            '<div class="tcm-share-header">',
            '  <h3 class="tcm-share-title" id="tcmShareTitle"><i class="bi bi-share"></i> Share with Friends</h3>',
            '  <button type="button" class="tcm-share-close" aria-label="Close modal">&times;</button>',
            '</div>',
            '<div class="tcm-share-body">',
            '  <div class="tcm-share-target-card">',
            '    <div class="tcm-share-target-title" id="tcmTargetTitle">—</div>',
            '    <p class="tcm-share-target-desc" id="tcmTargetDesc">—</p>',
            '  </div>',
            '  <div class="tcm-share-actions">',
            '    <button type="button" class="tcm-share-action-btn whatsapp" id="tcmShareWhatsApp">',
            '      <i class="bi bi-whatsapp"></i> WhatsApp',
            '    </button>',
            '    <button type="button" class="tcm-share-action-btn" id="tcmShareNative">',
            '      <i class="bi bi-grid-3x3-gap"></i> More Apps',
            '    </button>',
            '  </div>',
            '  <div class="tcm-share-link-bar">',
            '    <input type="text" class="tcm-share-link-input" id="tcmShareInput" readonly spellcheck="false">',
            '    <button type="button" class="tcm-share-copy-btn" id="tcmShareCopy">',
            '      <i class="bi bi-link-45deg"></i> Copy Link',
            '    </button>',
            '  </div>',
            '</div>'
        ].join('');

        overlayEl.appendChild(modalEl);
        document.body.appendChild(overlayEl);

        // Event listeners
        overlayEl.addEventListener('click', function (e) {
            if (e.target === overlayEl) closeModal();
        });

        modalEl.querySelector('.tcm-share-close').addEventListener('click', closeModal);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlayEl.classList.contains('active')) {
                closeModal();
            }
        });

        modalEl.querySelector('#tcmShareWhatsApp').addEventListener('click', function () {
            var msg = (currentShareData.title ? '*' + currentShareData.title + '*\n' : '') +
                (currentShareData.text ? currentShareData.text + '\n\n' : '\n') +
                currentShareData.url;
            var waUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(msg);
            window.open(waUrl, '_blank', 'noopener,noreferrer');
        });

        modalEl.querySelector('#tcmShareNative').addEventListener('click', function () {
            if (navigator.share) {
                navigator.share({
                    title: currentShareData.title,
                    text: currentShareData.text,
                    url: currentShareData.url
                }).catch(function () {});
            } else {
                // Fallback copy
                copyToClipboard(currentShareData.url);
            }
        });

        modalEl.querySelector('#tcmShareCopy').addEventListener('click', function () {
            copyToClipboard(currentShareData.url);
        });

        modalEl.querySelector('#tcmShareInput').addEventListener('click', function () {
            this.select();
        });
    }

    function openModal(data) {
        createModalElements();
        currentShareData = {
            title: data.title || document.title,
            text:  data.text  || '',
            url:   buildCleanShareUrl(data.url)
        };

        var titleEl = modalEl.querySelector('#tcmTargetTitle');
        var descEl  = modalEl.querySelector('#tcmTargetDesc');
        var inputEl = modalEl.querySelector('#tcmShareInput');
        var copyBtn = modalEl.querySelector('#tcmShareCopy');

        if (titleEl) titleEl.textContent = currentShareData.title;
        if (descEl)  descEl.textContent  = currentShareData.text || 'Check out this live session on TCM One';
        if (inputEl) inputEl.value       = currentShareData.url;
        if (copyBtn) {
            copyBtn.innerHTML = '<i class="bi bi-link-45deg"></i> Copy Link';
            copyBtn.classList.remove('copied');
        }

        // Hide "More Apps" if device doesn't support Web Share
        var nativeBtn = modalEl.querySelector('#tcmShareNative');
        if (nativeBtn) {
            nativeBtn.style.display = navigator.share ? 'flex' : 'none';
        }

        overlayEl.classList.add('active');
        overlayEl.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        if (overlayEl) {
            overlayEl.classList.remove('active');
            overlayEl.setAttribute('aria-hidden', 'true');
        }
    }

    function copyToClipboard(text) {
        var success = false;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                onCopiedSuccess();
            }).catch(function () {
                fallbackCopy(text);
            });
        } else {
            fallbackCopy(text);
        }
    }

    function fallbackCopy(text) {
        try {
            var tempInput = document.createElement('textarea');
            tempInput.value = text;
            tempInput.style.position = 'fixed';
            tempInput.style.left = '-9999px';
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            onCopiedSuccess();
        } catch (e) {
            prompt('Copy this link:', text);
        }
    }

    function onCopiedSuccess() {
        showToast('✓ Link Copied to Clipboard!');
        var copyBtn = document.getElementById('tcmShareCopy');
        if (copyBtn) {
            copyBtn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
            copyBtn.classList.add('copied');
            setTimeout(function () {
                copyBtn.innerHTML = '<i class="bi bi-link-45deg"></i> Copy Link';
                copyBtn.classList.remove('copied');
            }, 2500);
        }
    }

    /**
     * Primary share trigger. Tries Web Share API first on touch/mobile devices;
     * falls back to modal on desktop or unsupported devices.
     */
    function triggerShare(data) {
        var sharePayload = {
            title: data.title || document.title,
            text:  data.text  || '',
            url:   buildCleanShareUrl(data.url)
        };

        var isMobileOrTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || window.innerWidth <= 768;

        if (isMobileOrTouch && navigator.share) {
            navigator.share(sharePayload).catch(function (err) {
                // If user didn't explicitly abort, open modal
                if (err && err.name !== 'AbortError') {
                    openModal(sharePayload);
                }
            });
        } else {
            openModal(sharePayload);
        }
    }

    /**
     * Automatically scan and bind all share buttons on the page.
     */
    function init() {
        var buttons = document.querySelectorAll('.tcm-share-btn, [data-tcm-share]');
        buttons.forEach(function (btn) {
            if (btn._tcmShareBound) return;
            btn._tcmShareBound = true;

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var rawSlug = btn.getAttribute('data-share-slug');
                var rawType = btn.getAttribute('data-share-type') || 'workshop';
                var rawUrl  = btn.getAttribute('data-share-url');
                var title   = btn.getAttribute('data-share-title') || document.title;
                var text    = btn.getAttribute('data-share-text') || '';

                if (!rawUrl && rawSlug) {
                    var domain = window.location.origin;
                    if (rawType === 'course') {
                        rawUrl = domain + '/courses/' + encodeURIComponent(rawSlug);
                    } else {
                        rawUrl = domain + '/workshops/' + encodeURIComponent(rawSlug);
                    }
                }

                triggerShare({
                    title: title,
                    text:  text,
                    url:   rawUrl || window.location.href
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return {
        init:         init,
        share:        triggerShare,
        openModal:    openModal,
        closeModal:   closeModal,
        copy:         copyToClipboard,
        showToast:    showToast,
        buildCleanUrl: buildCleanShareUrl
    };
});
