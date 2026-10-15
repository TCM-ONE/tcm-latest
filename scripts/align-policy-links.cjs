const fs = require('node:fs');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const read = name => fs.readFileSync(path.join(root, name), 'utf8');
const update = (name, fn) => {
  const before = read(name);
  const after = fn(before);
  if (after !== before) fs.writeFileSync(path.join(root, name), after);
};
const identity = read('views/partials/business-identity.html');
for (const name of ['about.html', 'contact.html']) {
  update(name, text => text.replace('</main>', identity + '\n</main>'));
}
for (const name of ['views/public/about/index.php', 'views/public/about/contact.php']) {
  update(name, text => text.replace('</main>', "<?php readfile(BASE_PATH . '/views/partials/business-identity.html'); ?>\n</main>"));
}
const methods = 'Payment methods are those offered through official TCM payment instructions at enrolment. The current student flow accepts a payment reference or receipt for manual verification before access is activated. Gateway payment methods and any instalment arrangement are available only when expressly offered; please confirm with admissions before paying.';
for (const name of ['contact.html', 'views/public/about/contact.php']) {
  update(name, text => text
    .replace(/We accept all major Indian payment methods through Razorpay:[^<]+/, methods)
    .replaceAll('hello@thecodemunk.in', 'thecodemunk@gmail.com')
    .replaceAll('Campus workshops, accredited curricula', 'Campus workshops, practical curricula')
    .replaceAll('within 2 to 4 business hours', 'during support hours, usually within one business day'));
}
update('views/public/courses/show.php', text => text
  .replace(/\s*\$showEmi = [^;]+;\s*\$emiMonthly = [^;]+;/, '')
  .replace(/<!-- STRICT EMI RULE:[\s\S]*?<\?php endif; \?>/, '<p style="font-size:12px;line-height:1.6;margin-top:12px">Confirm the batch timetable and any written instalment arrangement with admissions before payment.</p>')
  .replace(/Approx\. \$<\?= round\(\$price \/ 83\.5\) \?> USD &middot; /, '')
  .replace("$duration = $course['duration'] ?? '8 Weeks';", "$duration = !empty($course['duration']) ? $course['duration'] : 'Confirm duration with admissions';")
  .replace('<strong>Outcome:</strong> Verified Certificate', "<strong>Certificate:</strong> <?= !empty($course['certificate']) ? 'Completion certificate, subject to requirements' : 'Not included' ?>")
  .replace('<!-- Actions -->', `<div style="font-size:12px;line-height:1.8;margin-bottom:20px"><p>Online course access is activated after payment verification, usually within 24 hours of complete payment details. Live classes follow the agreed batch timetable. No physical shipping applies.</p><a href="<?= base_url('/course-information') ?>">Fees &amp; delivery details</a> · <a href="<?= base_url('/refund-policy') ?>">Refund &amp; cancellation</a> · <a href="<?= base_url('/certificate-policy') ?>">Certificate requirements</a><p>Career assistance does not guarantee employment. <a href="<?= base_url('/internship-placement-policy') ?>">Read the career policy</a>.</p></div>\n<!-- Actions -->`));
update('views/student/payments/submit.php', text => text.replace('<button type="submit" class="ps2-submit">', `<p style="font-size:.8rem;line-height:1.7;margin:16px 0">Before payment, review the <a href="<?= base_url('/terms') ?>">Terms &amp; Conditions</a>, <a href="<?= base_url('/privacy') ?>">Privacy Policy</a>, <a href="<?= base_url('/refund-policy') ?>">Refund &amp; Cancellation Policy</a> and <a href="<?= base_url('/course-information') ?>">digital delivery details</a>. Access follows payment verification and the agreed batch schedule.</p>\n<button type="submit" class="ps2-submit">`));
update('views/public/institutions/index.php', text => text.replace(/Each certificate carries a unique cryptographic ID[^<]+/, 'Eligible certificates carry a TCM certificate number. Recruiters can request record verification through thecodemunk@gmail.com with the learner’s authorisation where required. See the Certificate Policy for issuance and verification details.'));
update('sitemap.html', text => text
  .replace('Privacy Policy (DPDP Act 2023)', 'Privacy Policy')
  .replace('<li><a href="/terms.html">Terms &amp; Conditions</a></li>', '<li><a href="/terms.html">Terms &amp; Conditions</a></li>\n<li><a href="/refund-policy">Refund &amp; Cancellation Policy</a></li>\n<li><a href="/course-information">Course Fees, Duration &amp; Delivery</a></li>\n<li><a href="/internship-placement-policy">Internship &amp; Placement Policy</a></li>\n<li><a href="/certificate-policy">Certificate Issuance &amp; Verification Policy</a></li>'));
update('index.html', text => text
  .replace('Get offers from 800+ <span class="font-editorial-serif">top companies.</span>', 'Build skills for your <span class="font-editorial-serif">next career step.</span>')
  .replace('Our learners and alumni work at high-growth startups, tech unicorns, and global engineering leaders.', 'Prepare through practical projects, portfolio development and interview guidance where included. Company logos below illustrate the technology industry; they do not establish a TCM hiring partnership or guarantee an offer.')
  .replace('aria-label="Top hiring partner companies"', 'aria-label="Illustrative technology companies"')
  .replace(/<button type="button" class="tcm-btn tcm-btn-primary" id="openPlacementReportBtn">[\s\S]*?<\/button>/, '<a href="/internship-placement-policy" class="tcm-btn tcm-btn-primary">Internship &amp; Placement Policy</a>')
  .replace(/<div class="tcm-modal-overlay" id="tcmPlacementOverlay"[\s\S]*?(?=\s*<!-- Certificate|\s*<!-- =|\s*<div class="tcm-modal-overlay" id="tcmDemo)/, '')
  .replace('Industry-Recognized Software Engineering<br>', 'TCM One Learning<br>')
  .replace('</section>\n\n    <!-- ========================================================================\n         SECTION 11: FOR COLLEGES', '<p class="tcm-container" style="font-size:13px;line-height:1.7;padding-top:20px">Certificate images are samples. The credential included with your programme and its completion requirements are stated before enrolment. <a href="/certificate-policy">Read our Certificate Issuance &amp; Verification Policy</a>.</p></section>\n\n    <!-- ========================================================================\n         SECTION 11: FOR COLLEGES'));
console.log('Aligned public identity, service claims and policy links.');
