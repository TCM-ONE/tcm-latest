<?php
$isEdit = $post !== null;
$action = $isEdit ? base_url('/admin/posts/' . $post['id']) : base_url('/admin/posts');
$val = static fn (string $k, $d = '') => e((string) ($post[$k] ?? $d));
$coverPreview = (string) ($post['cover_image'] ?? '');
if ($coverPreview !== '' && !preg_match('#^https?://#i', $coverPreview) && !str_starts_with($coverPreview, '/')) {
    $coverPreview = base_url('/uploads/' . ltrim($coverPreview, '/'));
}
?>
<div class="tcm-page-head">
    <div><h2><?= $isEdit ? 'Edit Tech Update' : 'New Tech Update' ?></h2><p>Write in plain text with clear paragraphs. The public page formats it for easy reading.</p></div>
    <a class="tcm-btn" href="<?= base_url('/admin/posts') ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="tcm-card">
        <div class="tcm-field"><label>Title</label><input class="tcm-input" name="title" value="<?= $val('title') ?>" required placeholder="e.g. What we learned while building an AI workflow"></div>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Category</label><input class="tcm-input" name="category" value="<?= $val('category', 'General') ?>"></div>
            <div class="tcm-field"><label>Tags (comma separated)</label><input class="tcm-input" name="tags" value="<?= $val('tags') ?>"></div>
        </div>
        <div class="tcm-field"><label>Short summary</label><textarea class="tcm-textarea" name="excerpt" style="min-height:70px;" placeholder="A clear one or two sentence summary shown on the listing page."><?= $val('excerpt') ?></textarea></div>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Cover image upload <span class="muted">(optional)</span></label><input class="tcm-input" type="file" name="cover_upload" accept="image/jpeg,image/png,image/webp,image/gif"><small class="muted">JPG, PNG, WebP, or GIF · maximum 5 MB.</small></div>
            <div class="tcm-field"><label>Or cover image URL <span class="muted">(optional)</span></label><input class="tcm-input" type="url" name="cover_image" value="<?= $val('cover_image') ?>" placeholder="https://…"><small class="muted">A selected file takes priority over this URL.</small></div>
        </div>
        <?php if ($isEdit && $coverPreview !== ''): ?><div class="tcm-field"><img src="<?= e($coverPreview) ?>" alt="Current cover" style="max-width:220px;max-height:120px;object-fit:cover;border:1px solid #e5e5e5;"><small class="muted" style="display:block;margin-top:6px;">Current cover image</small></div><?php endif; ?>
        <div class="tcm-field"><label>Article</label><textarea class="tcm-textarea" name="content" style="min-height:300px;" placeholder="Write the full update here. Separate paragraphs with a blank line."><?= $val('content') ?></textarea></div>
        <div class="tcm-field" style="max-width:200px;">
            <label>Status</label>
            <select class="tcm-select" name="status">
                <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>
        <button class="tcm-btn primary"><i class="bi bi-check2"></i> <?= $isEdit ? 'Save Update' : 'Save Update' ?></button>
    </div>
</form>
