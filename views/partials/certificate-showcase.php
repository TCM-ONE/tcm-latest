<?php
/**
 * Shared Certificate Showcase Section
 * Reusable across Course & Workshop detail pages.
 *
 * @var string|null $certEyebrow
 * @var string|null $certTitle
 * @var string|null $certDesc
 */

$certEyebrow = $certEyebrow ?? 'CERTIFICATIONS & CREDENTIALS';
$certTitle   = $certTitle ?? 'TCM One Learning';
$certDesc    = $certDesc ?? 'Earn official, verifiable credentials from TCM One and Cuboidsoft upon successful completion of your coursework and hands-on capstone project.';
?>
<!-- Certificate Showcase Section -->
<section class="tcm-cert-section" id="certifications" aria-labelledby="certifications-title">
  <div class="tcm-cert-heading">
    <div class="tcm-cert-eyebrow"><?= e($certEyebrow) ?></div>
    <h2 id="certifications-title" class="tcm-h2 tcm-cert-heading-title">
      <?= e($certTitle) ?><br>
      <span class="font-editorial-serif">Certifications</span>
    </h2>
    <p style="color: rgba(255, 255, 255, 0.7); max-width: 660px; margin: 14px auto 0; font-size: 15px; line-height: 1.6;">
      <?= e($certDesc) ?>
    </p>
  </div>

  <div class="tcm-cert-blueprint-wrap">
    <!-- Horizontal Guide Lines extending edge-to-edge -->
    <div class="tcm-cert-guide-line tcm-cert-guide-top" aria-hidden="true"></div>
    <div class="tcm-cert-guide-line tcm-cert-guide-bottom" aria-hidden="true"></div>

    <div class="tcm-container tcm-cert-container">
      <div class="tcm-cert-blueprint-grid">
        <!-- Top Intersection Nodes -->
        <span class="tcm-cert-node node-top-left" aria-hidden="true"></span>
        <span class="tcm-cert-node node-top-center" aria-hidden="true"></span>
        <span class="tcm-cert-node node-top-right" aria-hidden="true"></span>

        <!-- Outer Extended Nodes -->
        <span class="tcm-cert-node node-edge-left" aria-hidden="true"></span>
        <span class="tcm-cert-node node-edge-right" aria-hidden="true"></span>

        <!-- Left Cell: TCM One Certificate -->
        <div class="tcm-cert-cell">
          <button type="button" class="tcm-cert-card" data-cert-src="<?= base_url('/assets/cert-tcm-student.png') ?>" data-cert-title="TCM One Program Certificate" aria-label="View TCM One Program Certificate">
            <img src="<?= base_url('/assets/cert-tcm-student.png') ?>" alt="TCM One software engineering program certificate" class="tcm-cert-img" loading="lazy" width="1024" height="682">
          </button>
        </div>

        <!-- Right Cell: Cuboidsoft Partner Certificate -->
        <div class="tcm-cert-cell">
          <button type="button" class="tcm-cert-card" data-cert-src="<?= base_url('/assets/cert-nasscom-partner.png') ?>" data-cert-title="Cuboidsoft Partnership Certificate" aria-label="View Cuboidsoft Partnership Certificate">
            <img src="<?= base_url('/assets/cert-nasscom-partner.png') ?>" alt="Cuboidsoft and TCM One Recognised Training Partner Certificate" class="tcm-cert-img" loading="lazy" width="1024" height="724">
          </button>
        </div>

        <!-- Bottom Intersection Nodes -->
        <span class="tcm-cert-node node-bottom-left" aria-hidden="true"></span>
        <span class="tcm-cert-node node-bottom-center" aria-hidden="true"></span>
        <span class="tcm-cert-node node-bottom-right" aria-hidden="true"></span>

        <!-- Bottom Outer Extended Nodes -->
        <span class="tcm-cert-node node-bottom-edge-left" aria-hidden="true"></span>
        <span class="tcm-cert-node node-bottom-edge-right" aria-hidden="true"></span>
      </div>
    </div>
  </div>

  <!-- Trust Features & Policy Link -->
  <div style="text-align: center; margin-top: 36px; padding: 0 20px;">
    <div style="display: inline-flex; align-items: center; gap: 20px; flex-wrap: wrap; justify-content: center; font-size: 13px; color: rgba(255, 255, 255, 0.75);">
      <span style="display: inline-flex; align-items: center;"><i class="bi bi-shield-check" style="color: #10B981; margin-right: 6px; font-size: 15px;"></i> Tamper-Proof Verification</span>
      <span style="display: inline-flex; align-items: center;"><i class="bi bi-linkedin" style="color: #0A66C2; margin-right: 6px; font-size: 15px;"></i> 1-Click LinkedIn Shareable</span>
      <span style="display: inline-flex; align-items: center;"><i class="bi bi-patch-check" style="color: #F59E0B; margin-right: 6px; font-size: 15px;"></i> Recognized by Hiring Partners</span>
      <a href="<?= base_url('/certificate-policy.html') ?>" style="color: #FFFFFF; text-decoration: underline; text-underline-offset: 4px; font-weight: 500;">Certificate Policy &rarr;</a>
    </div>
  </div>
</section>

<!-- Certificate Zoom Modal -->
<div id="certZoomModal" class="tcm-modal-overlay" style="display: none; align-items: center; justify-content: center; z-index: 10001; background: rgba(0, 0, 0, 0.94); padding: 20px;" role="dialog" aria-modal="true" aria-labelledby="certZoomTitle">
  <div style="background: #111; border: 1px solid #444; max-width: 960px; width: 100%; padding: 24px; position: relative;">
    <button type="button" id="certZoomClose" style="position: absolute; top: 16px; right: 16px; background: transparent; border: 1px solid rgba(255,255,255,0.25); width: 34px; height: 34px; color: #FFFFFF; font-size: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;" aria-label="Close Preview">&times;</button>
    <div id="certZoomTitle" style="color: #FFFFFF; font-size: 16px; font-weight: 700; margin-bottom: 16px; padding-right: 48px;"></div>
    <div style="border: 1px solid rgba(255,255,255,0.1); background: #000; overflow: hidden; display: flex; align-items: center; justify-content: center;">
      <img id="certZoomImg" src="" alt="Certificate Preview" style="width: 100%; height: auto; max-height: 75vh; object-fit: contain; display: block;">
    </div>
  </div>
</div>
