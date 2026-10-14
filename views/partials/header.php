<?php
/**
 * Shared Public 2-Tier Header Component
 * Variables available:
 *   $activePage (string, optional) - 'workshops' | 'insights' | 'community' | 'contact' | 'partner'
 */
$activePage = $activePage ?? '';
$rootUrl = function_exists('base_url') ? base_url('/') : '/';
$workshopsUrl = function_exists('base_url') ? base_url('/workshops') : 'workshops.html';
$insightsUrl = function_exists('base_url') ? base_url('/insights.html') : 'insights.html';
$communityUrl = function_exists('base_url') ? base_url('/community.html') : 'community.html';
$contactUrl = function_exists('base_url') ? base_url('/contact.html') : 'contact.html';
$partnerUrl = function_exists('base_url') ? base_url('/partner.html') : 'partner.html';
$brochureUrl = function_exists('base_url') ? base_url('/download-brochure') : '/download-brochure';
$loginUrl = function_exists('base_url') ? base_url('/login.php') : 'login.php';
$logoImg = function_exists('base_url') ? base_url('/assets/logo.png') : './assets/logo.png';
?>
<!-- ===== TCM ONE 2-TIER UNIVERSAL HEADER ===== -->
<header class="header">
    <!-- Tier 1: Desktop Top Utility Bar (>= 1200px) -->
    <div class="header-top-bar d-none d-xl-block">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="header-top-left d-flex align-items-center gap-2">
                    <span class="header-top-kicker"><i class="bi bi-patch-check-fill"></i> Industry-Grade Live Learning</span>
                    <span class="header-top-divider">•</span>
                    <a href="<?= $brochureUrl ?>" class="header-top-brochure btn-trigger-brochure" data-trigger-brochure="true">
                        <i class="bi bi-file-earmark-pdf"></i> Download Official Course Brochure PDF
                    </a>
                </div>

                <div class="header-top-right d-flex align-items-center gap-3">
                    <!-- Language Picker -->
                    <div class="desktop-lang-picker mobile-lang-picker">
                        <button type="button" class="desktop-lang-toggle mobile-lang-toggle" aria-label="Change language" aria-expanded="false">
                            <i class="bi bi-translate"></i>
                            <span>Language</span>
                            <strong class="mobile-lang-code">EN</strong>
                        </button>
                        <div class="mobile-lang-menu" role="menu">
                            <button type="button" data-lang="en" role="menuitem">English</button>
                            <button type="button" data-lang="hi" role="menuitem">Hindi</button>
                            <button type="button" data-lang="mr" role="menuitem">Marathi</button>
                            <button type="button" data-lang="gu" role="menuitem">Gujarati</button>
                            <button type="button" data-lang="bn" role="menuitem">Bengali</button>
                            <button type="button" data-lang="ta" role="menuitem">Tamil</button>
                            <button type="button" data-lang="te" role="menuitem">Telugu</button>
                            <button type="button" data-lang="kn" role="menuitem">Kannada</button>
                            <button type="button" data-lang="ml" role="menuitem">Malayalam</button>
                            <button type="button" data-lang="pa" role="menuitem">Punjabi</button>
                            <button type="button" data-lang="ur" role="menuitem">Urdu</button>
                            <button type="button" data-lang="ar" role="menuitem">Arabic</button>
                            <button type="button" data-lang="fr" role="menuitem">French</button>
                            <button type="button" data-lang="es" role="menuitem">Spanish</button>
                            <button type="button" data-lang="de" role="menuitem">German</button>
                            <button type="button" data-lang="it" role="menuitem">Italian</button>
                            <button type="button" data-lang="pt" role="menuitem">Portuguese</button>
                            <button type="button" data-lang="ru" role="menuitem">Russian</button>
                            <button type="button" data-lang="ko" role="menuitem">Korean</button>
                            <button type="button" data-lang="zh-CN" role="menuitem">Chinese</button>
                            <button type="button" data-lang="ja" role="menuitem">Japanese</button>
                            <button type="button" data-lang="ne" role="menuitem">Nepali</button>
                            <button type="button" data-lang="th" role="menuitem">Thai</button>
                            <button type="button" data-lang="vi" role="menuitem">Vietnamese</button>
                        </div>
                    </div>

                    <span class="header-top-divider">|</span>
                    <a href="<?= $loginUrl ?>" class="header-top-login"><i class="bi bi-person"></i> Log in</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 2: Main Navigation Bar -->
    <div class="header-main-bar">
        <div class="container">
            <nav class="navbar navbar-expand-xl p-0">
                <div class="header-nav-top">
                    <!-- Column 1 (Left on Desktop): Brand Logo + Primary Nav Links -->
                    <div class="header-nav-left d-flex align-items-center">
                        <a class="navbar-brand logo" href="<?= $rootUrl ?>">
                            <img src="<?= $logoImg ?>" alt="TCM One Logo" class="logo-img">
                            <span class="logo-text"><span class="logo-main">TCM</span><span class="logo-one">ONE</span></span>
                        </a>

                        <!-- Desktop Navigation Links (Column 1) -->
                        <div class="header-desktop-links d-none d-xl-flex align-items-center">
                            <ul class="navbar-nav flex-row align-items-center">
                                <li class="nav-item">
                                    <a class="nav-link <?= $activePage === 'workshops' ? 'active' : '' ?>" href="<?= $workshopsUrl ?>">Workshops</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $activePage === 'insights' ? 'active' : '' ?>" href="<?= $insightsUrl ?>">TCM Insights</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $activePage === 'community' ? 'active' : '' ?>" href="<?= $communityUrl ?>">Community</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>" href="<?= $contactUrl ?>">Contact</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $activePage === 'partner' ? 'active' : '' ?>" href="<?= $partnerUrl ?>">Become a Partner</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Column 2 (Right on Desktop): Primary CTA "Book a Demo" -->
                    <div class="header-desktop-actions d-none d-xl-flex align-items-center">
                        <button type="button" class="join-btn demo-trigger">Book a Demo</button>
                    </div>

                    <!-- Mobile Header Right: Strictly Language + Book a Demo + Hamburger (< 1200px) -->
                    <div class="header-mobile-right d-xl-none">
                        <div class="mobile-lang-picker">
                            <button type="button" class="mobile-head-btn mobile-lang-toggle" aria-label="Change language" aria-expanded="false">
                                <i class="bi bi-translate"></i>
                                <span class="mobile-lang-code">EN</span>
                            </button>
                            <div class="mobile-lang-menu" role="menu">
                                <button type="button" data-lang="en" role="menuitem">English</button>
                                <button type="button" data-lang="hi" role="menuitem">Hindi</button>
                                <button type="button" data-lang="mr" role="menuitem">Marathi</button>
                                <button type="button" data-lang="gu" role="menuitem">Gujarati</button>
                                <button type="button" data-lang="bn" role="menuitem">Bengali</button>
                                <button type="button" data-lang="ta" role="menuitem">Tamil</button>
                                <button type="button" data-lang="te" role="menuitem">Telugu</button>
                                <button type="button" data-lang="kn" role="menuitem">Kannada</button>
                                <button type="button" data-lang="ml" role="menuitem">Malayalam</button>
                                <button type="button" data-lang="pa" role="menuitem">Punjabi</button>
                                <button type="button" data-lang="ur" role="menuitem">Urdu</button>
                                <button type="button" data-lang="ar" role="menuitem">Arabic</button>
                                <button type="button" data-lang="fr" role="menuitem">French</button>
                                <button type="button" data-lang="es" role="menuitem">Spanish</button>
                                <button type="button" data-lang="de" role="menuitem">German</button>
                                <button type="button" data-lang="it" role="menuitem">Italian</button>
                                <button type="button" data-lang="pt" role="menuitem">Portuguese</button>
                                <button type="button" data-lang="ru" role="menuitem">Russian</button>
                                <button type="button" data-lang="ko" role="menuitem">Korean</button>
                                <button type="button" data-lang="zh-CN" role="menuitem">Chinese</button>
                                <button type="button" data-lang="ja" role="menuitem">Japanese</button>
                                <button type="button" data-lang="ne" role="menuitem">Nepali</button>
                                <button type="button" data-lang="th" role="menuitem">Thai</button>
                                <button type="button" data-lang="vi" role="menuitem">Vietnamese</button>
                            </div>
                        </div>

                        <button type="button" class="mobile-head-btn mobile-demo-btn demo-trigger" aria-label="Book a Demo">
                            <span class="mobile-head-text">Book a Demo</span>
                        </button>

                        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Collapsible Drawer Menu (< 1200px) -->
                <div class="collapse navbar-collapse d-xl-none" id="navbarMenu">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'workshops' ? 'active' : '' ?>" href="<?= $workshopsUrl ?>">Workshops</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'insights' ? 'active' : '' ?>" href="<?= $insightsUrl ?>">TCM Insights</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'community' ? 'active' : '' ?>" href="<?= $communityUrl ?>">Community</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>" href="<?= $contactUrl ?>">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'partner' ? 'active' : '' ?>" href="<?= $partnerUrl ?>">Become a Partner</a>
                        </li>
                    </ul>

                    <div class="header-drawer-actions">
                        <a href="<?= $brochureUrl ?>" class="drawer-brochure-link btn-trigger-brochure" data-trigger-brochure="true">
                            <i class="bi bi-file-earmark-pdf"></i> Download Course Brochure PDF
                        </a>
                        <a href="<?= $loginUrl ?>" class="drawer-login-link">Log in</a>
                        <button type="button" class="join-btn drawer-demo-btn demo-trigger">Book a Free Demo</button>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>
