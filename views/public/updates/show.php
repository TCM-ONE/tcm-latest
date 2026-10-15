<?php
$date = $post['published_at'] ?: $post['created_at'] ?: null;
$readTime = max(1, (int) ceil(str_word_count(strip_tags((string) $post['content'])) / 220));

$coverUrl = '';
if (!empty($post['cover_image'])) {
    $img = (string) $post['cover_image'];
    if (preg_match('#^https?://#i', $img)) {
        $coverUrl = $img;
    } elseif (str_starts_with($img, 'assets/') || str_starts_with($img, '/assets/')) {
        $coverUrl = base_url('/' . ltrim($img, '/'));
    } elseif (str_starts_with($img, 'uploads/') || str_starts_with($img, '/uploads/')) {
        $coverUrl = base_url('/' . ltrim($img, '/'));
    } else {
        $coverUrl = base_url('/uploads/' . ltrim($img, '/'));
    }
}

if (!function_exists('renderPostContent')) {
    function renderPostContent(string $rawContent): string {
        $blocks = preg_split('/\R\s*\R/', trim($rawContent)) ?: [];
        $html = '';

        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            // Heading 2: ## Heading
            if (str_starts_with($block, '## ')) {
                $text = trim(substr($block, 3));
                $html .= '<h2 class="tcm-article-h2">' . e($text) . '</h2>' . "\n";
                continue;
            }

            // Heading 3: ### Heading
            if (str_starts_with($block, '### ')) {
                $text = trim(substr($block, 4));
                $html .= '<h3 class="tcm-article-h3">' . e($text) . '</h3>' . "\n";
                continue;
            }

            // Blockquote / Key Takeaway: > Quote
            if (str_starts_with($block, '> ')) {
                $lines = explode("\n", $block);
                $cleanLines = array_map(function($l) {
                    return ltrim(trim($l), '> ');
                }, $lines);
                $escaped = implode("<br>", array_map('e', $cleanLines));
                $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
                $html .= '<blockquote class="tcm-article-quote">' . $escaped . '</blockquote>' . "\n";
                continue;
            }

            // Check if block contains list items
            $rawLines = explode("\n", $block);
            $hasListItems = false;
            foreach ($rawLines as $l) {
                $tl = trim($l);
                if (preg_match('/^[-*]\s+/', $tl) || preg_match('/^\d+\.\s+/', $tl)) {
                    $hasListItems = true;
                    break;
                }
            }

            if ($hasListItems) {
                $inUl = false;
                $inOl = false;
                $pBuffer = [];

                foreach ($rawLines as $l) {
                    $tl = trim($l);
                    if ($tl === '') continue;

                    if (preg_match('/^[-*]\s+(.*)$/', $tl, $m)) {
                        if (!empty($pBuffer)) {
                            $pText = implode("<br>", array_map('e', $pBuffer));
                            $pText = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $pText);
                            $html .= '<p>' . $pText . '</p>' . "\n";
                            $pBuffer = [];
                        }
                        if ($inOl) {
                            $html .= '</ol>' . "\n";
                            $inOl = false;
                        }
                        if (!$inUl) {
                            $html .= '<ul class="tcm-article-list">' . "\n";
                            $inUl = true;
                        }
                        $item = e($m[1]);
                        $item = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $item);
                        $html .= '<li>' . $item . '</li>' . "\n";
                    } elseif (preg_match('/^\d+\.\s+(.*)$/', $tl, $m)) {
                        if (!empty($pBuffer)) {
                            $pText = implode("<br>", array_map('e', $pBuffer));
                            $pText = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $pText);
                            $html .= '<p>' . $pText . '</p>' . "\n";
                            $pBuffer = [];
                        }
                        if ($inUl) {
                            $html .= '</ul>' . "\n";
                            $inUl = false;
                        }
                        if (!$inOl) {
                            $html .= '<ol class="tcm-article-olist">' . "\n";
                            $inOl = true;
                        }
                        $item = e($m[1]);
                        $item = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $item);
                        $html .= '<li>' . $item . '</li>' . "\n";
                    } else {
                        if ($inUl) {
                            $html .= '</ul>' . "\n";
                            $inUl = false;
                        }
                        if ($inOl) {
                            $html .= '</ol>' . "\n";
                            $inOl = false;
                        }
                        $pBuffer[] = $tl;
                    }
                }

                if ($inUl) {
                    $html .= '</ul>' . "\n";
                }
                if ($inOl) {
                    $html .= '</ol>' . "\n";
                }
                if (!empty($pBuffer)) {
                    $pText = implode("<br>", array_map('e', $pBuffer));
                    $pText = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $pText);
                    $html .= '<p>' . $pText . '</p>' . "\n";
                }
                continue;
            }

            // Standard Paragraph
            $escaped = nl2br(e($block));
            $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
            $html .= '<p>' . $escaped . '</p>' . "\n";
        }

        return $html;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="app-base" content="<?= e(base_url('')) ?>">
  <title><?= e($title) ?></title>
  <meta name="description" content="<?= e($description) ?>">
  <link rel="canonical" href="<?= e($canonical) ?>">
  <meta property="og:type" content="article">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($description) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.15') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-updates.css?v=2.0') ?>">
</head>
<body>
<?php \TCM\Core\View::partial('partials.public-header'); ?>

<main id="mainContent">
  <article class="tcm-article">
    <header class="tcm-article-header">
      <div class="tcm-container">
        <a class="tcm-article-back" href="<?= base_url('/tech-updates') ?>"><i class="bi bi-arrow-left"></i> All tech updates</a>
        <div class="tcm-update-meta">
          <span><?= e((string) $post['category']) ?></span>
          <span><?= $date ? e(date('d M Y', strtotime((string) $date))) : '' ?></span>
          <span><?= $readTime ?> min read</span>
        </div>
        <h1 class="tcm-h1"><?= e((string) $post['title']) ?></h1>
        <?php if (!empty($post['excerpt'])): ?>
          <p><?= e((string) $post['excerpt']) ?></p>
        <?php endif; ?>
      </div>
    </header>

    <?php if (!empty($post['cover_image'])): ?>
      <div class="tcm-container">
        <img class="tcm-article-cover" src="<?= e($coverUrl) ?>" alt="<?= e((string) $post['title']) ?>">
      </div>
    <?php endif; ?>

    <div class="tcm-container">
      <div class="tcm-article-body">
        <?= renderPostContent((string) $post['content']) ?>
      </div>
    </div>
  </article>

  <section class="tcm-article-engagement" id="discussion">
    <div class="tcm-container">
      <div class="tcm-engagement-inner">

        <!-- Like Row -->
        <div class="tcm-like-row">
          <div>
            <span class="tcm-label">ARTICLE RESPONSE</span>
            <h2>Was this useful?</h2>
          </div>
          <?php if ($viewer !== null): ?>
            <form id="tcmLikeForm" method="post" action="<?= base_url('/tech-updates/' . rawurlencode((string) $post['slug']) . '/like') ?>">
              <?= csrf_field() ?>
              <button type="submit" id="tcmLikeBtn" class="tcm-like-button <?= $liked ? 'liked' : '' ?>" title="<?= $liked ? 'Unlike this update' : 'Like this update' ?>">
                <i class="bi <?= $liked ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                <span id="tcmLikeLabel"><?= $liked ? 'Liked' : 'Like this' ?></span>
                <strong id="tcmLikeCount"><?= $likes ?></strong>
              </button>
            </form>
          <?php else: ?>
            <button type="button" class="tcm-like-button open-tcm-auth-modal" data-auth-reason="like" aria-label="Sign in to like this update">
              <i class="bi bi-heart"></i>
              <span>Sign in to like</span>
              <strong><?= $likes ?></strong>
            </button>
          <?php endif; ?>
        </div>

        <!-- Comments Section -->
        <div class="tcm-comments">
          <div class="tcm-comments-head">
            <span class="tcm-label">JOIN THE CONVERSATION</span>
            <h2>Comments <span id="tcmCommentCount"><?= count($comments) ?></span></h2>
          </div>

          <?php if ($viewer !== null): ?>
            <form class="tcm-comment-form" id="tcmCommentForm" method="post" action="<?= base_url('/tech-updates/' . rawurlencode((string) $post['slug']) . '/comments') ?>">
              <?= csrf_field() ?>
              <label for="articleComment">Share a thoughtful response</label>
              <textarea id="articleComment" name="body" maxlength="2000" required placeholder="What did you find useful?"></textarea>
              <div id="tcmCommentFeedback" style="display:none; margin:10px 0; font-size:13px;"></div>
              <div>
                <small>Your comment appears after review.</small>
                <button class="tcm-btn tcm-btn-primary" id="tcmCommentSubmit" type="submit">Post comment</button>
              </div>
            </form>
          <?php else: ?>
            <div class="tcm-comment-signin">
              <p>Sign in to add your perspective to this conversation.</p>
              <button type="button" class="tcm-btn tcm-btn-secondary open-tcm-auth-modal" data-auth-reason="comment">
                Sign in to comment
              </button>
            </div>
          <?php endif; ?>

          <div class="tcm-comment-list" id="tcmCommentList">
            <?php foreach ($comments as $comment): ?>
              <article class="tcm-comment">
                <div class="tcm-comment-avatar"><?= e(strtoupper(mb_substr((string) $comment['name'], 0, 1))) ?></div>
                <div>
                  <header>
                    <strong><?= e((string) $comment['name']) ?></strong>
                    <time datetime="<?= e((string) $comment['created_at']) ?>"><?= e(date('d M Y', strtotime((string) $comment['created_at']))) ?></time>
                  </header>
                  <p><?= nl2br(e((string) $comment['body'])) ?></p>
                </div>
              </article>
            <?php endforeach; ?>
            <?php if ($comments === []): ?>
              <p class="tcm-comments-empty" id="tcmCommentsEmpty">No comments yet. Start the conversation.</p>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </section>

  <?php if ($related !== []): ?>
    <section class="tcm-related">
      <div class="tcm-container">
        <div class="tcm-eyebrow">KEEP READING</div>
        <h2 class="tcm-h2">More from <span class="font-editorial-serif"><?= e((string) $post['category']) ?></span></h2>
        <div class="tcm-related-grid">
          <?php foreach ($related as $item): ?>
            <a href="<?= base_url('/tech-updates/' . rawurlencode((string) $item['slug'])) ?>">
              <span><?= e((string) $item['category']) ?></span>
              <strong><?= e((string) $item['title']) ?></strong>
              <i class="bi bi-arrow-up-right"></i>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php if ($viewer === null): ?>
<!-- ========================================================================
     SIGN-IN MODAL FOR UNREGISTERED / GUEST VISITORS
     Keeps visitor on the exact same blog page upon authentication
     ======================================================================== -->
<div class="tcm-blog-auth-overlay" id="tcmBlogAuthModal" role="dialog" aria-modal="true" aria-labelledby="tcmAuthModalTitle" style="display:none;">
  <div class="tcm-blog-auth-backdrop"></div>
  <div class="tcm-blog-auth-dialog">
    <button type="button" class="tcm-blog-auth-close" id="tcmAuthModalClose" aria-label="Close dialog">
      <i class="bi bi-x-lg"></i>
    </button>
    <div class="tcm-blog-auth-head">
      <div class="tcm-blog-auth-eyebrow">TCM ONE COMMUNITY</div>
      <h3 class="tcm-blog-auth-title" id="tcmAuthModalTitle">Sign in to participate</h3>
      <p class="tcm-blog-auth-sub" id="tcmAuthModalSub">Sign in to like, leave comments, and join discussions on TCM One.</p>
    </div>

    <!-- Alert container -->
    <div class="tcm-blog-auth-alert" id="tcmAuthAlert" style="display:none;" role="alert"></div>

    <!-- Google Button -->
    <a href="<?= base_url('/auth/google?redirect=' . rawurlencode('/tech-updates/' . (string)$post['slug'] . '#discussion')) ?>" class="tcm-google-btn" id="tcmGoogleLoginBtn">
      <svg class="tcm-google-icon" viewBox="0 0 24 24" width="18" height="18" xmlns="http://www.w3.org/2000/svg">
        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
      </svg>
      <span>Continue with Google</span>
    </a>

    <div class="tcm-blog-auth-divider">
      <span>or sign in with email</span>
    </div>

    <!-- Direct Email / Password Form -->
    <form id="tcmModalLoginForm" method="post" action="<?= base_url('/auth/login') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="redirect" value="/tech-updates/<?= e((string)$post['slug']) ?>#discussion">
      <div class="tcm-form-field">
        <label for="modalEmailInput">Email address</label>
        <input type="email" id="modalEmailInput" name="email" required autocomplete="email" placeholder="you@example.com">
      </div>
      <div class="tcm-form-field">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <label for="modalPasswordInput">Password</label>
          <a href="<?= base_url('/auth/login') ?>" style="font-size:11px; color:var(--tcm-muted); text-decoration:none;">Forgot password?</a>
        </div>
        <div class="tcm-input-password-wrap">
          <input type="password" id="modalPasswordInput" name="password" required autocomplete="current-password" placeholder="••••••••">
          <button type="button" class="tcm-pw-eye" id="toggleModalPassword" aria-label="Toggle password visibility">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="tcm-btn tcm-btn-primary tcm-btn-block" id="modalLoginSubmit">
        <span class="btn-text">Sign In &amp; Continue</span>
        <span class="btn-spinner" style="display:none;"><i class="bi bi-arrow-repeat spin"></i></span>
      </button>
    </form>

    <div class="tcm-blog-auth-footer">
      <span>Don't have an account?</span>
      <a href="<?= base_url('/auth/register') ?>">Create an account &rarr;</a>
    </div>
  </div>
</div>
<?php endif; ?>

<?php \TCM\Core\View::partial('partials.public-footer'); ?>
<script src="<?= base_url('/assets/tcm-editorial.js?v=2.15') ?>"></script>

<script>
(function() {
  'use strict';

  // -------------------------------------------------------------------------
  // Auth Modal Controls (when unauthenticated)
  // -------------------------------------------------------------------------
  var modal = document.getElementById('tcmBlogAuthModal');
  var openTriggers = document.querySelectorAll('.open-tcm-auth-modal');
  var closeBtn = document.getElementById('tcmAuthModalClose');
  var backdrop = document.querySelector('.tcm-blog-auth-backdrop');
  var subTitle = document.getElementById('tcmAuthModalSub');

  function openAuthModal(reason) {
    if (!modal) return;
    if (subTitle) {
      if (reason === 'like') {
        subTitle.textContent = 'Sign in to like this update and support practical developer articles.';
      } else if (reason === 'comment') {
        subTitle.textContent = 'Sign in to share your perspective and join the discussion.';
      } else {
        subTitle.textContent = 'Sign in to like, leave comments, and join discussions on TCM One.';
      }
    }
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(function() {
      var emailInput = document.getElementById('modalEmailInput');
      if (emailInput) emailInput.focus();
    }, 100);
  }

  function closeAuthModal() {
    if (!modal) return;
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }

  openTriggers.forEach(function(trigger) {
    trigger.addEventListener('click', function(e) {
      e.preventDefault();
      var reason = trigger.getAttribute('data-auth-reason') || '';
      openAuthModal(reason);
    });
  });

  if (closeBtn) closeBtn.addEventListener('click', closeAuthModal);
  if (backdrop) backdrop.addEventListener('click', closeAuthModal);
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal && modal.style.display !== 'none') {
      closeAuthModal();
    }
  });

  // Toggle password visibility in modal
  var togglePwBtn = document.getElementById('toggleModalPassword');
  if (togglePwBtn) {
    togglePwBtn.addEventListener('click', function() {
      var pwInput = document.getElementById('modalPasswordInput');
      var icon = togglePwBtn.querySelector('i');
      if (!pwInput) return;
      if (pwInput.type === 'password') {
        pwInput.type = 'text';
        if (icon) icon.className = 'bi bi-eye-slash';
      } else {
        pwInput.type = 'password';
        if (icon) icon.className = 'bi bi-eye';
      }
    });
  }

  // Modal Login Form Submission (AJAX with instant reload to current page)
  var modalLoginForm = document.getElementById('tcmModalLoginForm');
  if (modalLoginForm) {
    modalLoginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      var emailEl = document.getElementById('modalEmailInput');
      var passwordEl = document.getElementById('modalPasswordInput');
      var alertBox = document.getElementById('tcmAuthAlert');
      var submitBtn = document.getElementById('modalLoginSubmit');
      var btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
      var btnSpin = submitBtn ? submitBtn.querySelector('.btn-spinner') : null;

      var email = emailEl ? emailEl.value.trim() : '';
      var password = passwordEl ? passwordEl.value : '';

      if (!email || !password) {
        if (alertBox) {
          alertBox.className = 'tcm-blog-auth-alert error';
          alertBox.textContent = 'Please enter both email and password.';
          alertBox.style.display = 'block';
        }
        return;
      }

      if (alertBox) {
        alertBox.style.display = 'none';
        alertBox.textContent = '';
      }

      if (btnText) btnText.style.display = 'none';
      if (btnSpin) btnSpin.style.display = 'inline-block';
      if (submitBtn) submitBtn.disabled = true;

      var currentPostUrl = window.location.pathname + '#discussion';

      fetch(modalLoginForm.action, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          email: email,
          password: password,
          redirect: currentPostUrl
        })
      })
      .then(function(res) {
        return res.json().then(function(json) {
          return { status: res.status, ok: res.ok, data: json };
        }).catch(function() {
          return { status: res.status, ok: res.ok, data: null };
        });
      })
      .then(function(result) {
        if (result.ok && result.data && result.data.success) {
          if (alertBox) {
            alertBox.className = 'tcm-blog-auth-alert success';
            alertBox.textContent = 'Signed in successfully! Updating...';
            alertBox.style.display = 'block';
          }
          // Stay on exact same post page and focus discussion
          window.location.hash = 'discussion';
          window.location.reload();
        } else {
          if (btnText) btnText.style.display = 'inline-block';
          if (btnSpin) btnSpin.style.display = 'none';
          if (submitBtn) submitBtn.disabled = false;
          if (alertBox) {
            alertBox.className = 'tcm-blog-auth-alert error';
            alertBox.textContent = (result.data && result.data.message) ? result.data.message : 'Invalid email or password.';
            alertBox.style.display = 'block';
          }
        }
      })
      .catch(function() {
        if (btnText) btnText.style.display = 'inline-block';
        if (btnSpin) btnSpin.style.display = 'none';
        if (submitBtn) submitBtn.disabled = false;
        if (alertBox) {
          alertBox.className = 'tcm-blog-auth-alert error';
          alertBox.textContent = 'Unable to reach the server. Please try again.';
          alertBox.style.display = 'block';
        }
      });
    });
  }

  // -------------------------------------------------------------------------
  // Logged-in User AJAX Like Button
  // -------------------------------------------------------------------------
  var likeForm = document.getElementById('tcmLikeForm');
  if (likeForm) {
    likeForm.addEventListener('submit', function(e) {
      e.preventDefault();
      var likeBtn = document.getElementById('tcmLikeBtn');
      var likeIcon = likeBtn ? likeBtn.querySelector('i') : null;
      var likeLabel = document.getElementById('tcmLikeLabel');
      var likeCount = document.getElementById('tcmLikeCount');

      var isLiked = likeBtn && likeBtn.classList.contains('liked');
      var currentVal = parseInt(likeCount ? likeCount.textContent : '0', 10) || 0;

      // Optimistic update
      var nextLiked = !isLiked;
      var nextVal = nextLiked ? (currentVal + 1) : Math.max(0, currentVal - 1);

      if (likeBtn) likeBtn.classList.toggle('liked', nextLiked);
      if (likeIcon) likeIcon.className = 'bi ' + (nextLiked ? 'bi-heart-fill' : 'bi-heart');
      if (likeLabel) likeLabel.textContent = nextLiked ? 'Liked' : 'Like this';
      if (likeCount) likeCount.textContent = nextVal;

      fetch(likeForm.action, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: JSON.stringify({})
      })
      .then(function(res) { return res.json(); })
      .then(function(json) {
        if (json && json.success && json.data) {
          if (likeCount) likeCount.textContent = json.data.likes;
          if (likeBtn) likeBtn.classList.toggle('liked', json.data.liked);
          if (likeIcon) likeIcon.className = 'bi ' + (json.data.liked ? 'bi-heart-fill' : 'bi-heart');
          if (likeLabel) likeLabel.textContent = json.data.liked ? 'Liked' : 'Like this';
        }
      })
      .catch(function() {
        // Rollback
        if (likeBtn) likeBtn.classList.toggle('liked', isLiked);
        if (likeIcon) likeIcon.className = 'bi ' + (isLiked ? 'bi-heart-fill' : 'bi-heart');
        if (likeLabel) likeLabel.textContent = isLiked ? 'Liked' : 'Like this';
        if (likeCount) likeCount.textContent = currentVal;
      });
    });
  }

  // -------------------------------------------------------------------------
  // Logged-in User AJAX Comment Submission
  // -------------------------------------------------------------------------
  var commentForm = document.getElementById('tcmCommentForm');
  if (commentForm) {
    commentForm.addEventListener('submit', function(e) {
      e.preventDefault();
      var textarea = document.getElementById('articleComment');
      var feedback = document.getElementById('tcmCommentFeedback');
      var submitBtn = document.getElementById('tcmCommentSubmit');
      var body = textarea ? textarea.value.trim() : '';

      if (!body || body.length < 3) return;

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Posting...';
      }

      fetch(commentForm.action, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ body: body })
      })
      .then(function(res) { return res.json(); })
      .then(function(json) {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Post comment';
        }
        if (json && json.success) {
          if (textarea) textarea.value = '';
          if (feedback) {
            feedback.style.display = 'block';
            feedback.style.color = '#15803d';
            feedback.style.background = '#f0fdf4';
            feedback.style.border = '1px solid #bbf7d0';
            feedback.style.padding = '10px 14px';
            feedback.style.borderRadius = '8px';
            feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i> Thanks! Your comment has been submitted and is awaiting approval.';
          }
        } else {
          if (feedback) {
            feedback.style.display = 'block';
            feedback.style.color = '#dc2626';
            feedback.style.background = '#fef2f2';
            feedback.style.border = '1px solid #fecaca';
            feedback.style.padding = '10px 14px';
            feedback.style.borderRadius = '8px';
            feedback.textContent = (json && json.message) ? json.message : 'Could not submit comment. Please try again.';
          }
        }
      })
      .catch(function() {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Post comment';
        }
        if (feedback) {
          feedback.style.display = 'block';
          feedback.style.color = '#dc2626';
          feedback.style.background = '#fef2f2';
          feedback.style.border = '1px solid #fecaca';
          feedback.style.padding = '10px 14px';
          feedback.style.borderRadius = '8px';
          feedback.textContent = 'Network error. Please try again.';
        }
      });
    });
  }

})();
</script>
</body>
</html>
