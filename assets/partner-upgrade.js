/**
 * TCM One — Become a Partner B2B Upgrade
 * Interactive Revenue Calculator, 2-Step Application Wizard & Consultation Booking
 */

(function () {
    'use strict';

    // ==========================================
    // Analytics & Event Tracking Helper
    // ==========================================
    function trackPartnerEvent(eventName, payload) {
        try {
            var eventData = payload || {};
            // 1. Dispatch custom DOM event
            var customEvt = new CustomEvent('tcm:partner_event', {
                detail: { event: eventName, data: eventData }
            });
            window.dispatchEvent(customEvt);

            // 2. Google Analytics 4 (if configured)
            if (typeof window.gtag === 'function') {
                window.gtag('event', eventName, eventData);
            }

            // 3. Meta Pixel (if configured)
            if (typeof window.fbq === 'function') {
                window.fbq('trackCustom', eventName, eventData);
            }
        } catch (e) {
            // Silently ignore tracking errors to avoid blocking user flow
        }
    }

    // Base URL resolution for XAMPP / subfolder and production
    var BASE = (function () {
        if (typeof window !== 'undefined' && window.TCM_BASE != null) {
            return String(window.TCM_BASE).replace(/\/$/, '');
        }
        var meta = typeof document !== 'undefined' ? document.querySelector('meta[name="app-base"]') : null;
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
        if (typeof window !== 'undefined' && window.location && window.location.pathname) {
            var match = window.location.pathname.match(/^(\/tcm)(?:\/|$)/i);
            if (match) return match[1];
        }
        return '';
    })();

    function apiUrl(path) {
        var clean = '/' + String(path || '').replace(/^\/+/, '');
        return BASE ? BASE + clean : clean;
    }

    // Currency Formatter (INR)
    function formatINR(val) {
        var num = Math.round(Number(val) || 0);
        return '₹' + num.toLocaleString('en-IN');
    }

    // ==========================================
    // 1. SMART PARTNERSHIP REVENUE CALCULATOR
    // ==========================================
    var calcState = {
        model: 'Academic Partnership', // 'Referral' or 'Academic Partnership'
        students: 40,
        fee: 5000,
        percent: 40,
        batches: 2
    };

    var calcElements = {
        modelReferralBtn: document.getElementById('calcModelReferral'),
        modelAcademicBtn: document.getElementById('calcModelAcademic'),
        studentSlider: document.getElementById('calcStudentSlider'),
        studentVal: document.getElementById('calcStudentVal'),
        feeSlider: document.getElementById('calcFeeSlider'),
        feeVal: document.getElementById('calcFeeVal'),
        batchesSlider: document.getElementById('calcBatchesSlider'),
        batchesVal: document.getElementById('calcBatchesVal'),
        pctSlider: document.getElementById('calcPctSlider'),
        pctVal: document.getElementById('calcPctVal'),
        pctPresets: document.querySelectorAll('.ptr-pct-btn'),
        // Outputs
        outTotal: document.getElementById('calcOutTotal'),
        outInstitute: document.getElementById('calcOutInstitute'),
        outTcm: document.getElementById('calcOutTcm'),
        outPerBatch: document.getElementById('calcOutPerBatch'),
        outBatchesCombined: document.getElementById('calcOutBatchesCombined'),
        outModelLabel: document.getElementById('calcOutModelLabel'),
        discussBtn: document.getElementById('calcDiscussBtn')
    };

    function updateCalculator() {
        if (!calcElements.outInstitute) return;

        var totalPerBatch = calcState.students * calcState.fee;
        var institutePerBatch = Math.round((totalPerBatch * calcState.percent) / 100);
        var tcmPerBatch = totalPerBatch - institutePerBatch;

        var totalCollections = totalPerBatch * calcState.batches;
        var totalInstituteEarnings = institutePerBatch * calcState.batches;
        var totalTcmRevenue = totalCollections - totalInstituteEarnings;

        if (calcElements.outInstitute) calcElements.outInstitute.textContent = formatINR(totalInstituteEarnings);
        if (calcElements.outTotal) calcElements.outTotal.textContent = formatINR(totalCollections);
        if (calcElements.outTcm) calcElements.outTcm.textContent = formatINR(totalTcmRevenue);
        if (calcElements.outPerBatch) calcElements.outPerBatch.textContent = formatINR(institutePerBatch);
        if (calcElements.outBatchesCombined) calcElements.outBatchesCombined.textContent = formatINR(totalInstituteEarnings) + ' across ' + calcState.batches + ' batch' + (calcState.batches > 1 ? 'es' : '');
        if (calcElements.outModelLabel) calcElements.outModelLabel.textContent = calcState.model === 'Referral' ? 'Est. Referral Commission' : 'Est. Institute Revenue Share';

        // Update pills
        if (calcElements.studentVal) calcElements.studentVal.textContent = calcState.students + ' Students';
        if (calcElements.feeVal) calcElements.feeVal.textContent = formatINR(calcState.fee);
        if (calcElements.batchesVal) calcElements.batchesVal.textContent = calcState.batches + ' Batch' + (calcState.batches > 1 ? 'es' : '');
        if (calcElements.pctVal) calcElements.pctVal.textContent = calcState.percent + '%';

        // Highlight preset buttons
        if (calcElements.pctPresets) {
            calcElements.pctPresets.forEach(function (btn) {
                var p = parseInt(btn.getAttribute('data-pct'), 10);
                if (p === calcState.percent) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
    }

    function initCalculator() {
        if (!calcElements.modelAcademicBtn) return;

        calcElements.modelReferralBtn.addEventListener('click', function () {
            calcState.model = 'Referral';
            calcState.percent = 15; // approved referral commission
            calcElements.pctSlider.value = 15;
            calcElements.pctSlider.min = 15;
            calcElements.pctSlider.max = 15;
            calcElements.modelReferralBtn.classList.add('active');
            calcElements.modelAcademicBtn.classList.remove('active');
            updateCalculator();
            trackPartnerEvent('partnership_model_selected', { model: 'Referral' });
        });

        calcElements.modelAcademicBtn.addEventListener('click', function () {
            calcState.model = 'Academic Partnership';
            calcState.percent = 40; // approved academic partnership
            calcElements.pctSlider.value = 40;
            calcElements.pctSlider.min = 40;
            calcElements.pctSlider.max = 40;
            calcElements.modelAcademicBtn.classList.add('active');
            calcElements.modelReferralBtn.classList.remove('active');
            updateCalculator();
            trackPartnerEvent('partnership_model_selected', { model: 'Academic Partnership' });
        });

        calcElements.studentSlider.addEventListener('input', function (e) {
            calcState.students = parseInt(e.target.value, 10);
            updateCalculator();
        });

        calcElements.feeSlider.addEventListener('input', function (e) {
            calcState.fee = parseInt(e.target.value, 10);
            updateCalculator();
        });

        calcElements.batchesSlider.addEventListener('input', function (e) {
            calcState.batches = parseInt(e.target.value, 10);
            updateCalculator();
        });

        calcElements.pctSlider.addEventListener('input', function (e) {
            calcState.percent = parseInt(e.target.value, 10);
            updateCalculator();
        });

        calcElements.pctPresets.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var p = parseInt(btn.getAttribute('data-pct'), 10);
                calcState.percent = p;
                calcElements.pctSlider.value = p;
                updateCalculator();
            });
        });

        // "Discuss This Partnership" CTA
        if (calcElements.discussBtn) {
            calcElements.discussBtn.addEventListener('click', function (e) {
                e.preventDefault();
                trackPartnerEvent('revenue_calculator_used', {
                    model: calcState.model,
                    students: calcState.students,
                    fee: calcState.fee,
                    batches: calcState.batches
                });

                // Pre-fill form fields
                var modelSelect = document.getElementById('ptrModelInput');
                if (modelSelect) {
                    modelSelect.value = calcState.model === 'Referral' ? 'Referral Model' : 'Partnership Model';
                }
                var studentInput = document.getElementById('ptrStudentsInput');
                if (studentInput) {
                    studentInput.value = calcState.students;
                }
                var batchInput = document.getElementById('ptrBatchesInput');
                if (batchInput) {
                    batchInput.value = calcState.batches;
                }

                // Smooth scroll to form
                var target = document.getElementById('partnership-application');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }

        updateCalculator();
    }

    // ==========================================
    // 2. TWO-STEP PARTNERSHIP APPLICATION WIZARD
    // ==========================================
    var formWizard = {
        form: document.getElementById('tcmPartnerForm'),
        pane1: document.getElementById('ptrStep1Pane'),
        pane2: document.getElementById('ptrStep2Pane'),
        stepIndicator1: document.getElementById('ptrStepIndicator1'),
        stepIndicator2: document.getElementById('ptrStepIndicator2'),
        toStep2Btn: document.getElementById('ptrToStep2Btn'),
        backToStep1Btn: document.getElementById('ptrBackToStep1Btn'),
        submitBtn: document.getElementById('ptrSubmitBtn'),
        successCard: document.getElementById('ptrSuccessCard')
    };

    function validateStep1() {
        var isValid = true;
        var requiredIds = [
            'ptrInstName',
            'ptrContactPerson',
            'ptrPhone',
            'ptrEmail',
            'ptrCity',
            'ptrModelInput'
        ];

        requiredIds.forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            var val = (el.value || '').trim();
            if (val === '') {
                el.classList.add('is-invalid');
                isValid = false;
            } else if (id === 'ptrEmail' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                el.classList.add('is-invalid');
                isValid = false;
            } else if (id === 'ptrPhone' && val.replace(/\D/g, '').length < 8) {
                el.classList.add('is-invalid');
                isValid = false;
            } else {
                el.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    function initFormWizard() {
        if (!formWizard.form || !formWizard.toStep2Btn) return;

        // Input blur cleanup
        formWizard.form.querySelectorAll('input, select, textarea').forEach(function (input) {
            input.addEventListener('input', function () {
                if (input.classList.contains('is-invalid')) {
                    input.classList.remove('is-invalid');
                }
            });
        });

        // Step 1 -> Step 2
        formWizard.toStep2Btn.addEventListener('click', function () {
            if (!validateStep1()) {
                var firstInvalid = formWizard.form.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            formWizard.pane1.classList.remove('active');
            formWizard.pane2.classList.add('active');

            if (formWizard.stepIndicator1) {
                formWizard.stepIndicator1.classList.remove('active');
                formWizard.stepIndicator1.classList.add('completed');
            }
            if (formWizard.stepIndicator2) {
                formWizard.stepIndicator2.classList.add('active');
            }

            trackPartnerEvent('partner_application_started', {
                institution: (document.getElementById('ptrInstName') || {}).value,
                model: (document.getElementById('ptrModelInput') || {}).value
            });
        });

        // Step 2 -> Step 1 (Back)
        if (formWizard.backToStep1Btn) {
            formWizard.backToStep1Btn.addEventListener('click', function () {
                formWizard.pane2.classList.remove('active');
                formWizard.pane1.classList.add('active');

                if (formWizard.stepIndicator2) {
                    formWizard.stepIndicator2.classList.remove('active');
                }
                if (formWizard.stepIndicator1) {
                    formWizard.stepIndicator1.classList.add('active');
                    formWizard.stepIndicator1.classList.remove('completed');
                }
            });
        }

        // Form Submit
        formWizard.form.addEventListener('submit', function (e) {
            e.preventDefault();

            var consent = document.getElementById('consentCheck');
            if (consent && !consent.checked) {
                alert('Please acknowledge the partnership consent checkbox before submitting.');
                consent.focus();
                return;
            }

            // Collect form data
            var payload = {
                institution_name: (document.getElementById('ptrInstName') || {}).value || '',
                institution_type: (document.getElementById('ptrInstType') || {}).value || 'College',
                city: (document.getElementById('ptrCity') || {}).value || '',
                state: (document.getElementById('ptrState') || {}).value || '',
                website: (document.getElementById('ptrWebsite') || {}).value || '',
                contact_person: (document.getElementById('ptrContactPerson') || {}).value || '',
                designation: (document.getElementById('ptrDesignation') || {}).value || '',
                phone: (document.getElementById('ptrPhone') || {}).value || '',
                email: (document.getElementById('ptrEmail') || {}).value || '',
                partnership_model: (document.getElementById('ptrModelInput') || {}).value || 'Academic Partnership',
                expected_students: parseInt((document.getElementById('ptrStudentsInput') || {}).value, 10) || 0,
                expected_batches: parseInt((document.getElementById('ptrBatchesInput') || {}).value, 10) || 1,
                has_own_mentors: (document.getElementById('ptrMentorsInput') || {}).value || 'Not Specified',
                has_lab_facility: (document.getElementById('ptrLabInput') || {}).value || 'Available',
                expected_start_date: (document.getElementById('ptrStartDate') || {}).value || null,
                courses_of_interest: (document.getElementById('ptrCoursesInput') || {}).value || '',
                requirements: (document.getElementById('ptrReqsInput') || {}).value || '',
                calculator_estimates: {
                    model: calcState.model,
                    estimated_students: calcState.students,
                    course_fee: calcState.fee,
                    batches: calcState.batches
                }
            };

            // Loading state
            var originalBtnHtml = formWizard.submitBtn.innerHTML;
            formWizard.submitBtn.disabled = true;
            formWizard.submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Submitting Application...';

            fetch(apiUrl('/api/partner/apply'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                formWizard.submitBtn.disabled = false;
                formWizard.submitBtn.innerHTML = originalBtnHtml;

                if (data && data.success) {
                    var refCode = (data.data && data.data.ref_code) ? data.data.ref_code : 'TCM-PTR-RECORDED';
                    var waUrl = (data.data && data.data.whatsapp_url) ? data.data.whatsapp_url : 'https://wa.me/917987823907';

                    trackPartnerEvent('partner_application_submitted', {
                        ref_code: refCode,
                        institution: payload.institution_name,
                        model: payload.partnership_model
                    });

                    // Hide form & show verified confirmation card
                    formWizard.form.style.display = 'none';
                    if (formWizard.successCard) {
                        formWizard.successCard.style.display = 'block';
                        var refEl = document.getElementById('ptrSuccessRefCode');
                        if (refEl) refEl.textContent = refCode;
                        var waBtn = document.getElementById('ptrSuccessWaBtn');
                        if (waBtn) waBtn.href = waUrl;
                    }
                } else {
                    var errMsg = (data && data.message) ? data.message : 'Submission failed. Please check your inputs and try again.';
                    alert(errMsg);
                }
            })
            .catch(function () {
                formWizard.submitBtn.disabled = false;
                formWizard.submitBtn.innerHTML = originalBtnHtml;
                alert('Connection error. Please check your network or reach out to our team directly on WhatsApp (+91 79878 23907).');
            });
        });
    }

    // ==========================================
    // 3. BOOK A CONSULTATION CALL
    // ==========================================
    function initConsultationMeeting() {
        var meetingForm = document.getElementById('ptrMeetingForm');
        var meetingSuccess = document.getElementById('ptrMeetingSuccess');
        if (!meetingForm) return;

        // Ensure minimum date is tomorrow
        var dateInput = document.getElementById('ptrMeetingDate');
        if (dateInput) {
            var tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            var yyyy = tomorrow.getFullYear();
            var mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
            var dd = String(tomorrow.getDate()).padStart(2, '0');
            dateInput.min = yyyy + '-' + mm + '-' + dd;
        }

        meetingForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var payload = {
                institution_name: (document.getElementById('ptrMeetInstName') || {}).value || '',
                contact_person: (document.getElementById('ptrMeetPerson') || {}).value || '',
                phone: (document.getElementById('ptrMeetPhone') || {}).value || '',
                email: (document.getElementById('ptrMeetEmail') || {}).value || '',
                preferred_date: (document.getElementById('ptrMeetingDate') || {}).value || '',
                preferred_time: (document.getElementById('ptrMeetingTime') || {}).value || '11:00 AM - 12:00 PM',
                partnership_model: (document.getElementById('ptrMeetModel') || {}).value || 'Academic Partnership',
                notes: (document.getElementById('ptrMeetNotes') || {}).value || ''
            };

            var btn = meetingForm.querySelector('button[type="submit"]');
            var origText = btn ? btn.innerHTML : 'Request Meeting';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Scheduling...';
            }

            fetch(apiUrl('/api/partner/meeting'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
                if (data && data.success) {
                    trackPartnerEvent('partnership_meeting_requested', {
                        institution: payload.institution_name,
                        date: payload.preferred_date
                    });

                    meetingForm.style.display = 'none';
                    if (meetingSuccess) {
                        meetingSuccess.style.display = 'block';
                        var refSpan = document.getElementById('ptrMeetingRefCode');
                        if (refSpan && data.data) refSpan.textContent = data.data.ref_code || 'TCM-CALL-LOGGED';
                        var waLink = document.getElementById('ptrMeetingWaBtn');
                        if (waLink && data.data && data.data.whatsapp_url) waLink.href = data.data.whatsapp_url;
                    }
                } else {
                    alert((data && data.message) ? data.message : 'Could not schedule meeting. Please try again.');
                }
            })
            .catch(function () {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
                alert('Connection error. You can also chat directly on WhatsApp at +91 79878 23907.');
            });
        });
    }

    // ==========================================
    // 4. MATRIX TABLE MODEL SELECTION HOOKS
    // ==========================================
    function initMatrixHooks() {
        document.querySelectorAll('[data-select-partner-model]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var model = btn.getAttribute('data-select-partner-model');
                if (model === 'Referral') {
                    if (calcElements.modelReferralBtn) calcElements.modelReferralBtn.click();
                } else {
                    if (calcElements.modelAcademicBtn) calcElements.modelAcademicBtn.click();
                }

                var calcSection = document.getElementById('calculator');
                if (calcSection) {
                    calcSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    }

    // ==========================================
    // INITIALIZATION
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
        initCalculator();
        initFormWizard();
        initConsultationMeeting();
        initMatrixHooks();
        trackPartnerEvent('partner_page_view', { path: window.location.pathname });
    });

})();
