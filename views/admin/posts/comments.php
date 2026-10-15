<div class="tcm-page-head">
  <div><h2>Update Comments</h2><p>Review reader comments before they appear on Tech Updates articles.</p></div>
  <a class="tcm-btn" href="<?= base_url('/admin/posts') ?>"><i class="bi bi-newspaper"></i> Tech Updates</a>
</div>
<div class="tcm-card" style="overflow-x:auto;">
  <table class="tcm-table">
    <thead><tr><th>Reader &amp; article</th><th>Comment</th><th>Submitted</th><th>Status</th><th>Moderate</th></tr></thead>
    <tbody>
    <?php foreach ($comments as $comment): ?>
      <tr>
        <td><strong><?= e((string) $comment['user_name']) ?></strong><br><span class="muted"><?= e((string) $comment['user_email']) ?></span><br><a href="<?= base_url('/tech-updates/' . rawurlencode((string) $comment['post_slug'])) ?>" target="_blank" class="muted"><?= e((string) $comment['post_title']) ?></a></td>
        <td style="min-width:240px;white-space:pre-wrap;"><?= e((string) $comment['body']) ?></td>
        <td class="muted"><?= e(date('d M Y, h:i A', strtotime((string) $comment['created_at']))) ?></td>
        <td><span class="tcm-badge <?= $comment['status'] === 'approved' ? 'green' : ($comment['status'] === 'pending' ? 'amber' : '') ?>"><?= e((string) $comment['status']) ?></span></td>
        <td><div class="d-flex gap-8"><form method="post" action="<?= base_url('/admin/post-comments/' . $comment['id']) ?>"><?= csrf_field() ?><input type="hidden" name="status" value="approved"><button class="tcm-btn sm" type="submit">Approve</button></form><form method="post" action="<?= base_url('/admin/post-comments/' . $comment['id']) ?>"><?= csrf_field() ?><input type="hidden" name="status" value="hidden"><button class="tcm-btn sm danger" type="submit">Hide</button></form></div></td>
      </tr>
    <?php endforeach; ?>
    <?php if ($comments === []): ?><tr><td colspan="5" class="muted">No reader comments yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
