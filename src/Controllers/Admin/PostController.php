<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\FirebaseNotification;
use TCM\Core\Request;
use TCM\Core\Upload;
use TCM\Models\Notification;
use TCM\Models\Post;

final class PostController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');
        $this->view('admin/posts/index', [
            'title' => 'Tech Updates',
            'posts' => Post::all('created_at DESC'),
        ], 'admin');
    }

    public function create(): void
    {
        Auth::require('admin');
        $this->view('admin/posts/form', ['title' => 'New Tech Update', 'post' => null], 'admin');
    }

    public function store(): void
    {
        $user = Auth::require('admin');
        $this->validate([
            'title'  => 'required|min:3|max:200',
            'status' => 'in:draft,published',
        ], '/admin/posts/create');

        $status = Request::string('status', 'draft');
        $coverImage = $this->resolveCoverImage();
        $id = Post::create([
            'author_id'    => $user['id'],
            'title'        => Request::string('title'),
            'slug'         => $this->uniqueSlug(slugify(Request::string('title'))),
            'excerpt'      => Request::string('excerpt'),
            'content'      => Request::string('content'),
            'category'     => Request::string('category', 'General'),
            'tags'         => Request::string('tags'),
            'cover_image'  => $coverImage,
            'status'       => $status,
            'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null,
        ]);

        // 🔔 In-app + push: notify all students when a blog post is published
        if ($status === 'published') {
            Notification::ensureTable();
            Notification::toStudents(
                '📝 New Insight — ' . Request::string('title'),
                Request::string('title') . ' — Read the latest from The Code Munk.',
                '📝',
                base_url('/tech-updates')
            );
            FirebaseNotification::notifyAllStudents(
                '📝 New Insight — ' . Request::string('title'),
                Request::string('title') . ' — Read the latest from The Code Munk.',
                ['tag' => 'blog-new'],
                base_url('/tech-updates')
            );
        }

        $this->respond(['id' => $id], 'Tech update saved.', '/admin/posts');
    }

    public function edit(array $params): void
    {
        Auth::require('admin');
        $post = Post::find((int) $params['id']);
        if ($post === null) {
            flash('error', 'Post not found.');
            redirect('/admin/posts');
        }
        $this->view('admin/posts/form', ['title' => 'Edit Tech Update', 'post' => $post], 'admin');
    }

    public function update(array $params): void
    {
        Auth::require('admin');
        $id = (int) $params['id'];
        $post = Post::find($id);
        if ($post === null) {
            flash('error', 'Post not found.');
            redirect('/admin/posts');
        }
        $this->validate([
            'title'  => 'required|min:3|max:200',
            'status' => 'in:draft,published',
        ], '/admin/posts/' . $id . '/edit');

        $status = Request::string('status', 'draft');
        $coverImage = $this->resolveCoverImage((string) ($post['cover_image'] ?? ''));
        $slug = $post['slug'];
        if (Request::string('title') !== $post['title']) {
            $slug = $this->uniqueSlug(slugify(Request::string('title')), $id);
        }
        Post::update($id, [
            'title'        => Request::string('title'),
            'slug'         => $slug,
            'excerpt'      => Request::string('excerpt'),
            'content'      => Request::string('content'),
            'category'     => Request::string('category', 'General'),
            'tags'         => Request::string('tags'),
            'cover_image'  => $coverImage,
            'status'       => $status,
            'published_at' => $status === 'published' ? ($post['published_at'] ?? date('Y-m-d H:i:s')) : null,
        ]);
        $this->respond(['id' => $id], 'Tech update saved.', '/admin/posts');
    }

    public function destroy(array $params): void
    {
        Auth::require('admin');
        Post::delete((int) $params['id']);
        $this->respond(null, 'Tech update deleted.', '/admin/posts');
    }

    public function comments(): void
    {
        Auth::require('admin');
        $this->view('admin/posts/comments', [
            'title' => 'Update Comments',
            'comments' => Database::all(
                "SELECT c.*, p.title AS post_title, p.slug AS post_slug, u.name AS user_name, u.email AS user_email FROM post_comments c INNER JOIN posts p ON p.id = c.post_id INNER JOIN users u ON u.id = c.user_id ORDER BY FIELD(c.status, 'pending', 'approved', 'hidden'), c.created_at DESC"
            ),
        ], 'admin');
    }

    public function updateComment(array $params): void
    {
        Auth::require('admin');
        $status = Request::string('status');
        if (!in_array($status, ['approved', 'hidden'], true)) {
            flash('error', 'Choose a valid moderation status.');
            redirect('/admin/post-comments');
        }
        Database::update('post_comments', ['status' => $status], ['id' => (int) $params['id']]);
        $this->respond(null, $status === 'approved' ? 'Comment approved.' : 'Comment hidden.', '/admin/post-comments');
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 1;
        while (true) {
            $sql = 'SELECT COUNT(*) FROM posts WHERE slug = ?';
            $params = [$slug];
            if ($ignoreId !== null) {
                $sql .= ' AND id <> ?';
                $params[] = $ignoreId;
            }
            if ((int) Database::scalar($sql, $params) === 0) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }

    private function resolveCoverImage(string $existing = ''): string
    {
        $image = Request::string('cover_image');
        $file = $_FILES['cover_upload'] ?? null;

        if (!Upload::present($file)) {
            return $image;
        }

        try {
            $filename = Upload::store(
                $file,
                config('uploads.path') . '/tech-updates',
                ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                5 * 1024 * 1024
            );
        } catch (\RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('/admin/posts' . ($existing !== '' ? '' : '/create'));
        }

        if (str_starts_with($existing, 'tech-updates/')) {
            $oldPath = config('uploads.path') . '/' . $existing;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return 'tech-updates/' . $filename;
    }
}
