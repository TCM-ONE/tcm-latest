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
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="app-base" content="<?= e(base_url('')) ?>"><title><?= e($title) ?></title><meta name="description" content="<?= e($description) ?>"><link rel="canonical" href="<?= e($canonical) ?>"><meta property="og:type" content="article"><meta property="og:title" content="<?= e($title) ?>"><meta property="og:description" content="<?= e($description) ?>"><meta property="og:image" content="<?= e($ogImage) ?>"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.15') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-updates.css?v=1.1') ?>"></head><body>
<?php \TCM\Core\View::partial('partials.public-header'); ?><main id="mainContent"><article class="tcm-article"><header class="tcm-article-header"><div class="tcm-container"><a class="tcm-article-back" href="<?= base_url('/tech-updates') ?>"><i class="bi bi-arrow-left"></i> All tech updates</a><div class="tcm-update-meta"><span><?= e((string) $post['category']) ?></span><span><?= $date ? e(date('d M Y', strtotime((string) $date))) : '' ?></span><span><?= $readTime ?> min read</span></div><h1 class="tcm-h1"><?= e((string) $post['title']) ?></h1><?php if (!empty($post['excerpt'])): ?><p><?= e((string) $post['excerpt']) ?></p><?php endif; ?></div></header><?php if (!empty($post['cover_image'])): ?><div class="tcm-container"><img class="tcm-article-cover" src="<?= e($coverUrl) ?>" alt=""></div><?php endif; ?><div class="tcm-container"><div class="tcm-article-body"><?= renderPostContent((string) $post['content']) ?></div></div></article><section class="tcm-article-engagement" id="discussion"><div class="tcm-container"><div class="tcm-engagement-inner"><div class="tcm-like-row"><div><span class="tcm-label">ARTICLE RESPONSE</span><h2>Was this useful?</h2></div><?php if ($viewer !== null): ?><form method="post" action="<?= base_url('/tech-updates/' . rawurlencode((string) $post['slug']) . '/like') ?>"><?= csrf_field() ?><button type="submit" class="tcm-like-button <?= $liked ? 'liked' : '' ?>"><i class="bi <?= $liked ? 'bi-heart-fill' : 'bi-heart' ?>"></i><span><?= $liked ? 'Liked' : 'Like this' ?></span><strong><?= $likes ?></strong></button></form><?php else: ?><a class="tcm-like-button" href="<?= base_url('/login') ?>"><i class="bi bi-heart"></i><span>Sign in to like</span><strong><?= $likes ?></strong></a><?php endif; ?></div><div class="tcm-comments"><div class="tcm-comments-head"><span class="tcm-label">JOIN THE CONVERSATION</span><h2>Comments <span><?= count($comments) ?></span></h2></div><?php if ($viewer !== null): ?><form class="tcm-comment-form" method="post" action="<?= base_url('/tech-updates/' . rawurlencode((string) $post['slug']) . '/comments') ?>"><?= csrf_field() ?><label for="articleComment">Share a thoughtful response</label><textarea id="articleComment" name="body" maxlength="2000" required placeholder="What did you find useful?"></textarea><div><small>Your comment appears after review.</small><button class="tcm-btn tcm-btn-primary" type="submit">Post comment</button></div></form><?php else: ?><div class="tcm-comment-signin"><p>Sign in to add your perspective to this conversation.</p><a href="<?= base_url('/login') ?>" class="tcm-btn tcm-btn-secondary">Sign in to comment</a></div><?php endif; ?><div class="tcm-comment-list"><?php foreach ($comments as $comment): ?><article class="tcm-comment"><div class="tcm-comment-avatar"><?= e(strtoupper(mb_substr((string) $comment['name'], 0, 1))) ?></div><div><header><strong><?= e((string) $comment['name']) ?></strong><time datetime="<?= e((string) $comment['created_at']) ?>"><?= e(date('d M Y', strtotime((string) $comment['created_at']))) ?></time></header><p><?= nl2br(e((string) $comment['body'])) ?></p></div></article><?php endforeach; ?><?php if ($comments === []): ?><p class="tcm-comments-empty">No comments yet. Start the conversation.</p><?php endif; ?></div></div></div></div></section><?php if ($related !== []): ?><section class="tcm-related"><div class="tcm-container"><div class="tcm-eyebrow">KEEP READING</div><h2 class="tcm-h2">More from <span class="font-editorial-serif"><?= e((string) $post['category']) ?></span></h2><div class="tcm-related-grid"><?php foreach ($related as $item): ?><a href="<?= base_url('/tech-updates/' . rawurlencode((string) $item['slug'])) ?>"><span><?= e((string) $item['category']) ?></span><strong><?= e((string) $item['title']) ?></strong><i class="bi bi-arrow-up-right"></i></a><?php endforeach; ?></div></div></section><?php endif; ?></main><?php \TCM\Core\View::partial('partials.public-footer'); ?><script src="<?= base_url('/assets/tcm-editorial.js?v=2.15') ?>"></script></body></html>
