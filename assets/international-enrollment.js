(() => {
  const timezone = document.querySelector('#enrollment-timezone');
  const batch = document.querySelector('#enrollment-batch');
  const currency = document.querySelector('#enrollment-currency');
  const estimate = document.querySelector('#enrollment-estimate');
  const form = document.querySelector('[data-international-enrollment]');

  function track(name, extra = {}) {
    if (typeof window.gtag !== 'function') return;
    try {
      const consent = JSON.parse(localStorage.getItem('tcm_cookie_consent_v1') || 'null');
      if (consent?.version !== 1 || consent.analytics !== true) return;
      window.gtag('event', name, {
        course_slug: form.dataset.internationalEnrollment,
        display_currency: currency.value,
        ...extra,
      });
    } catch (_) {}
  }

  function formatBatches() {
    const format = new Intl.DateTimeFormat('en-US', {
      timeZone: timezone.value, dateStyle: 'medium', timeStyle: 'short',
    });
    batch.querySelectorAll('[data-start]').forEach(option => {
      option.textContent = format.format(new Date(option.dataset.start));
    });
  }

  if (timezone.dataset.detect === '1') {
    const detected = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (Array.from(timezone.options).some(option => option.value === detected)) timezone.value = detected;
  }
  timezone.addEventListener('change', formatBatches);
  currency.addEventListener('change', () => {
    estimate.textContent = currency.value === 'INR' ? '' : currency.selectedOptions[0].dataset.estimate;
  });
  form.addEventListener('input', () => track('international_enrollment_started'), { once: true });
  batch.addEventListener('change', () => {
    if (batch.value) track('international_cohort_selected', { selection: batch.value === 'request' ? 'timing_request' : 'scheduled_cohort' });
  });
  form.addEventListener('submit', () => {
    track('international_enrollment_form_submitted', { enrollment_path: batch.value === 'request' ? 'timing_request' : 'bank_transfer' });
  });
  formatBatches();
})();
