<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\Upload;
use TCM\Models\Testimonial;

/**
 * Handles ancillary CMS content: testimonials, categories, contact messages
 * and site settings.
 */
final class ContentController extends Controller
{
    // --- Categories ------------------------------------------------------
    public function categories(): void
    {
        Auth::require('admin');
        $this->view('admin/categories/index', [
            'title'      => 'Categories',
            'categories' => Database::all('SELECT * FROM categories ORDER BY sort_order, name'),
        ], 'admin');
    }

    public function storeCategory(): void
    {
        Auth::require('admin');
        $this->validate(['name' => 'required|min:2|max:120'], '/admin/categories');
        Database::insert('categories', [
            'name'       => Request::string('name'),
            'slug'       => slugify(Request::string('name')),
            'description' => Request::string('description'),
            'icon'       => Request::string('icon', 'bi-collection'),
            'audience'   => Request::string('audience', 'general'),
            'sort_order' => Request::int('sort_order'),
            'status'     => 'active',
        ]);
        $this->respond(null, 'Category added.', '/admin/categories');
    }

    public function deleteCategory(array $params): void
    {
        Auth::require('admin');
        Database::delete('categories', ['id' => (int) $params['id']]);
        $this->respond(null, 'Category removed.', '/admin/categories');
    }

    // --- Testimonials ----------------------------------------------------
    public function testimonials(): void
    {
        Auth::require('admin');
        $this->view('admin/testimonials/index', [
            'title'        => 'Testimonials',
            'testimonials' => Testimonial::all('sort_order, id'),
        ], 'admin');
    }

    public function storeTestimonial(): void
    {
        Auth::require('admin');
        $this->validate([
            'name'    => 'required|max:120',
            'content' => 'required',
        ], '/admin/testimonials');
        Testimonial::create([
            'name'       => Request::string('name'),
            'role'       => Request::string('role'),
            'content'    => Request::string('content'),
            'rating'     => min(5, max(1, Request::int('rating', 5))),
            'status'     => 'active',
            'sort_order' => Request::int('sort_order'),
        ]);
        $this->respond(null, 'Testimonial added.', '/admin/testimonials');
    }

    public function deleteTestimonial(array $params): void
    {
        Auth::require('admin');
        Testimonial::delete((int) $params['id']);
        $this->respond(null, 'Testimonial removed.', '/admin/testimonials');
    }

    // --- Contact messages ------------------------------------------------
    public function messages(): void
    {
        Auth::require('admin');
        Database::run("UPDATE contact_messages SET status = 'read' WHERE status = 'new'");
        $this->view('admin/messages/index', [
            'title'    => 'Messages',
            'messages' => Database::all('SELECT * FROM contact_messages ORDER BY created_at DESC'),
        ], 'admin');
    }

    public function deleteMessage(array $params): void
    {
        Auth::require('admin');
        Database::delete('contact_messages', ['id' => (int) $params['id']]);
        $this->respond(null, 'Message deleted.', '/admin/messages');
    }

    // --- Settings --------------------------------------------------------
    public function settings(): void
    {
        Auth::require('admin');
        $rows = Database::all('SELECT * FROM settings');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        $this->view('admin/settings/index', [
            'title'    => 'Site Settings',
            'settings' => $settings,
        ], 'admin');
    }

    public function saveSettings(): void
    {
        Auth::require('admin');
        $allowed = [
            'site_name', 'site_tagline', 'contact_email', 'contact_phone',
            'students_count', 'courses_count', 'currency',
            'whatsapp_number', 'whatsapp_message',
            // Payment details
            'payment_upi_id', 'payment_upi_name',
            'payment_bank_name', 'payment_account_name',
            'payment_account_number', 'payment_ifsc',
            'payment_note',
        ];
        foreach ($allowed as $key) {
            $value = Request::string($key);
            Database::run(
                'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                [$key, $value]
            );
        }

        // Handle optional brochure upload if provided in settings form
        if (Upload::present($_FILES['brochure'] ?? null)) {
            try {
                $destDir = config('uploads.path') . '/brochure';
                $filename = Upload::store(
                    $_FILES['brochure'],
                    $destDir,
                    ['pdf', 'doc', 'docx'],
                    20 * 1024 * 1024
                );
                $oldBrochure = Database::scalar("SELECT `value` FROM settings WHERE `key` = 'brochure_file'");
                if (!empty($oldBrochure)) {
                    $oldPath = config('uploads.path') . '/' . $oldBrochure;
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $origName = basename((string)($_FILES['brochure']['name'] ?? 'brochure.pdf'));
                $fileSize = (int)($_FILES['brochure']['size'] ?? 0);
                $sizeStr = $fileSize > 1048576
                    ? number_format($fileSize / 1048576, 2) . ' MB'
                    : number_format($fileSize / 1024, 1) . ' KB';
                $now = date('Y-m-d H:i:s');
                $entries = [
                    'brochure_file'       => 'brochure/' . $filename,
                    'brochure_name'       => $origName,
                    'brochure_size'       => $sizeStr,
                    'brochure_updated_at' => $now,
                ];
                foreach ($entries as $k => $v) {
                    Database::run(
                        'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                        [$k, $v]
                    );
                }
            } catch (\Throwable $e) {
                $this->respond('Settings saved, but brochure upload failed: ' . $e->getMessage(), null, '/admin/settings');
                return;
            }
        }

        $this->respond(null, 'Settings saved.', '/admin/settings');
    }

    public function uploadBrochure(): void
    {
        Auth::require('admin');

        $file = $_FILES['brochure'] ?? null;
        $redirectUrl = Request::string('redirect_to') ?: '/admin#admBrochureCard';

        if (!Upload::present($file)) {
            $this->respond('Please choose a valid PDF, DOC or DOCX brochure file to upload.', null, $redirectUrl);
            return;
        }

        try {
            $destDir = config('uploads.path') . '/brochure';
            $filename = Upload::store(
                $file,
                $destDir,
                ['pdf', 'doc', 'docx'],
                20 * 1024 * 1024 // 20 MB limit
            );

            // Fetch old brochure to remove it cleanly
            $oldBrochure = Database::scalar("SELECT `value` FROM settings WHERE `key` = 'brochure_file'");
            if (!empty($oldBrochure)) {
                $oldPath = config('uploads.path') . '/' . $oldBrochure;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $origName = basename((string)($file['name'] ?? 'brochure.pdf'));
            $fileSize = (int)($file['size'] ?? 0);
            $sizeStr = $fileSize > 1048576
                ? number_format($fileSize / 1048576, 2) . ' MB'
                : number_format($fileSize / 1024, 1) . ' KB';
            $now = date('Y-m-d H:i:s');

            $entries = [
                'brochure_file'       => 'brochure/' . $filename,
                'brochure_name'       => $origName,
                'brochure_size'       => $sizeStr,
                'brochure_updated_at' => $now,
            ];

            foreach ($entries as $k => $v) {
                Database::run(
                    'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                    [$k, $v]
                );
            }

            $this->respond(null, 'Brochure uploaded successfully.', $redirectUrl);
        } catch (\Throwable $e) {
            $this->respond('Upload failed: ' . $e->getMessage(), null, $redirectUrl);
        }
    }

    public function deleteBrochure(): void
    {
        Auth::require('admin');

        $redirectUrl = Request::string('redirect_to') ?: '/admin#admBrochureCard';
        $oldBrochure = Database::scalar("SELECT `value` FROM settings WHERE `key` = 'brochure_file'");
        if (!empty($oldBrochure)) {
            $oldPath = config('uploads.path') . '/' . $oldBrochure;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        Database::run("DELETE FROM settings WHERE `key` IN ('brochure_file', 'brochure_name', 'brochure_size', 'brochure_updated_at')");

        $this->respond(null, 'Brochure deleted successfully.', $redirectUrl);
    }
}
