<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Core\Auth;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Models\Post;

final class PublicUpdatesController extends Controller
{
    public function index(): void
    {
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $category = trim(Request::string('category'));
        $categories = Database::all("SELECT DISTINCT category FROM posts WHERE status = 'published' AND category <> '' ORDER BY category ASC");

        $sql = "SELECT id, title, slug, excerpt, content, cover_image, category, tags, published_at, created_at, views FROM posts WHERE status = 'published'";
        $params = [];
        if ($category !== '') {
            $sql .= ' AND category = ?';
            $params[] = $category;
        }
        $sql .= ' ORDER BY published_at DESC, id DESC';

        $this->view('public/updates/index', [
            'title'       => 'Tech Updates — TCM One',
            'description' => 'Practical notes, engineering articles, and learning updates from TCM One.',
            'canonical'   => $appUrl . '/tech-updates',
            'ogImage'     => $appUrl . '/assets/tcm-og-share.png',
            'posts'       => Database::all($sql, $params),
            'categories'  => $categories,
            'activeCategory' => $category,
        ], null);
    }

    public function show(array $params): void
    {
        $slug = (string) ($params['slug'] ?? '');
        $post = Post::findBySlug($slug);
        if ($post === null || ($post['status'] ?? '') !== 'published') {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Tech Update Not Found — TCM One',
                'message' => 'This update is unavailable. Browse our latest tech updates instead.',
            ], null);
            return;
        }

        Database::run('UPDATE posts SET views = views + 1 WHERE id = ?', [(int) $post['id']]);
        $post['views'] = (int) $post['views'] + 1;
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $related = Database::all(
            "SELECT id, title, slug, excerpt, category, published_at FROM posts WHERE status = 'published' AND id <> ? AND category = ? ORDER BY published_at DESC, id DESC LIMIT 3",
            [(int) $post['id'], (string) $post['category']]
        );
        $comments = Database::all(
            "SELECT c.id, c.body, c.created_at, u.name FROM post_comments c INNER JOIN users u ON u.id = c.user_id WHERE c.post_id = ? AND c.status = 'approved' ORDER BY c.created_at ASC",
            [(int) $post['id']]
        );
        $likes = (int) Database::scalar("SELECT COUNT(*) FROM post_reactions WHERE post_id = ? AND reaction = 'like'", [(int) $post['id']]);
        $user = Auth::user();
        $liked = $user !== null && (int) Database::scalar(
            "SELECT COUNT(*) FROM post_reactions WHERE post_id = ? AND user_id = ? AND reaction = 'like'",
            [(int) $post['id'], (int) $user['id']]
        ) > 0;

        $this->view('public/updates/show', [
            'title'       => $post['title'] . ' — TCM One Tech Updates',
            'description' => $this->description((string) ($post['excerpt'] ?: $post['content'])),
            'canonical'   => $appUrl . '/tech-updates/' . rawurlencode((string) $post['slug']),
            'ogImage'     => !empty($post['cover_image']) ? $this->coverUrl((string) $post['cover_image']) : ($appUrl . '/assets/tcm-og-share.png'),
            'post'        => $post,
            'related'     => $related,
            'comments'    => $comments,
            'likes'       => $likes,
            'liked'       => $liked,
            'viewer'      => $user,
        ], null);
    }

    public function toggleLike(array $params): void
    {
        $user = Auth::require();
        $post = $this->publishedPost((string) ($params['slug'] ?? ''));
        if ($post === null) {
            if (Request::isJson()) {
                Response::error('This update is unavailable.', 404);
            }
            flash('error', 'This update is unavailable.');
            redirect('/tech-updates');
        }

        $existing = (int) Database::scalar(
            "SELECT COUNT(*) FROM post_reactions WHERE post_id = ? AND user_id = ? AND reaction = 'like'",
            [(int) $post['id'], (int) $user['id']]
        );
        $liked = false;
        if ($existing > 0) {
            Database::run("DELETE FROM post_reactions WHERE post_id = ? AND user_id = ? AND reaction = 'like'", [(int) $post['id'], (int) $user['id']]);
            $msg = 'Like removed.';
            $liked = false;
        } else {
            Database::insert('post_reactions', ['post_id' => (int) $post['id'], 'user_id' => (int) $user['id'], 'reaction' => 'like']);
            $msg = 'Thanks for the like.';
            $liked = true;
        }

        $totalLikes = (int) Database::scalar("SELECT COUNT(*) FROM post_reactions WHERE post_id = ? AND reaction = 'like'", [(int) $post['id']]);

        if (Request::isJson()) {
            Response::success([
                'liked' => $liked,
                'likes' => $totalLikes,
            ], $msg);
        }

        flash('success', $msg);
        redirect('/tech-updates/' . rawurlencode((string) $post['slug']) . '#discussion');
    }

    public function storeComment(array $params): void
    {
        $user = Auth::require();
        $post = $this->publishedPost((string) ($params['slug'] ?? ''));
        if ($post === null) {
            if (Request::isJson()) {
                Response::error('This update is unavailable.', 404);
            }
            flash('error', 'This update is unavailable.');
            redirect('/tech-updates');
        }
        $body = trim(Request::string('body'));
        if (mb_strlen($body) < 3 || mb_strlen($body) > 2000) {
            if (Request::isJson()) {
                Response::error('Your comment must be between 3 and 2,000 characters.', 422);
            }
            flash('error', 'Your comment must be between 3 and 2,000 characters.');
            redirect('/tech-updates/' . rawurlencode((string) $post['slug']) . '#discussion');
        }
        Database::insert('post_comments', ['post_id' => (int) $post['id'], 'user_id' => (int) $user['id'], 'body' => $body, 'status' => 'pending']);
        if (Request::isJson()) {
            Response::success([
                'status' => 'pending',
                'name' => $user['name'],
                'body' => $body,
                'initial' => strtoupper(mb_substr((string) $user['name'], 0, 1)),
                'date' => date('d M Y'),
            ], 'Thanks—your comment is awaiting review.');
        }
        flash('success', 'Thanks—your comment is awaiting review.');
        redirect('/tech-updates/' . rawurlencode((string) $post['slug']) . '#discussion');
    }

    private function description(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');
        return mb_strlen($text) > 155 ? rtrim(mb_substr($text, 0, 152)) . '...' : $text;
    }

    private function coverUrl(string $path): string
    {
        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            return $path;
        }
        return base_url('/uploads/' . ltrim($path, '/'));
    }

    private function publishedPost(string $slug): ?array
    {
        $post = Post::findBySlug($slug);
        return $post !== null && ($post['status'] ?? '') === 'published' ? $post : null;
    }
}
