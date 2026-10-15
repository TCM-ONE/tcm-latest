<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\FirebaseNotification;
use TCM\Core\Request;
use TCM\Models\Notification;

/**
 * Google OAuth 2.0 Login
 *
 * Flow:
 *   1. /auth/google          → redirect user to Google consent screen
 *   2. /auth/google/callback → Google redirects back here with ?code=...
 *      - Exchange code for access token
 *      - Fetch user profile from Google
 *      - Find or create local user
 *      - Log them in and redirect to dashboard
 */
final class GoogleAuthController extends Controller
{
    private const GOOGLE_AUTH_URL     = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const GOOGLE_TOKEN_URL    = 'https://oauth2.googleapis.com/token';
    private const GOOGLE_USERINFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';

    /** Step 1 – Redirect to Google */
    public function redirect(): void
    {
        $clientId    = config('google.client_id');
        $redirectUri = config('google.redirect_uri');

        if (empty($clientId)) {
            flash('error', 'Google login is not configured yet.');
            redirect('/auth/login');
        }

        // Store intended return URL (e.g. blog post /tech-updates/{slug}#discussion)
        $returnTo = Request::string('redirect') ?: Request::string('return_to');
        if ($returnTo !== '' && str_starts_with($returnTo, '/') && !str_starts_with($returnTo, '//')) {
            $_SESSION['auth_return_to'] = $returnTo;
        }

        // CSRF state token stored in session
        $token = bin2hex(random_bytes(16));
        $_SESSION['google_oauth_state'] = $token;

        $statePayload = [
            'token'     => $token,
            'return_to' => $returnTo !== '' ? $returnTo : null,
        ];
        $state = rtrim(strtr(base64_encode((string) json_encode($statePayload)), '+/', '-_'), '=');

        $params = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'state'         => $state,
            'prompt'        => 'select_account',
        ]);

        header('Location: ' . self::GOOGLE_AUTH_URL . '?' . $params);
        exit;
    }

    /** Step 2 – Handle callback from Google */
    public function callback(): void
    {
        // Validate state to prevent CSRF
        $rawState     = $_GET['state'] ?? '';
        $sessionState = $_SESSION['google_oauth_state'] ?? '';
        unset($_SESSION['google_oauth_state']);

        $returnTo = $_SESSION['auth_return_to'] ?? null;
        unset($_SESSION['auth_return_to']);

        $tokenValid = false;
        if (!empty($rawState)) {
            if ($rawState === $sessionState) {
                $tokenValid = true;
            } else {
                $padded = strtr($rawState, '-_', '+/');
                $padLen = (4 - (strlen($padded) % 4)) % 4;
                if ($padLen > 0) {
                    $padded .= str_repeat('=', $padLen);
                }
                $decoded = json_decode((string) base64_decode($padded), true);
                if (is_array($decoded) && isset($decoded['token']) && hash_equals((string) $sessionState, (string) $decoded['token'])) {
                    $tokenValid = true;
                    if (!empty($decoded['return_to'])) {
                        $returnTo = (string) $decoded['return_to'];
                    }
                }
            }
        }

        if (!$tokenValid) {
            flash('error', 'Invalid OAuth state. Please try again.');
            redirect('/auth/login');
        }

        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            $error = $_GET['error'] ?? 'unknown';
            flash('error', 'Google login failed: ' . $error);
            redirect('/auth/login');
        }

        // Exchange code for tokens
        $tokens = $this->exchangeCode($code);
        if ($tokens === null) {
            flash('error', 'Failed to connect with Google. Please try again.');
            redirect('/auth/login');
        }

        // Fetch user info from Google
        $googleUser = $this->fetchUserInfo($tokens['access_token']);
        if ($googleUser === null) {
            flash('error', 'Could not retrieve your Google profile.');
            redirect('/auth/login');
        }

        // Find or create local user
        $user   = $this->findOrCreate($googleUser);
        $isNew  = $user['_is_new'] ?? false;
        unset($user['_is_new']);

        if ($user['status'] !== 'active') {
            flash('error', 'Your account has been suspended. Please contact support.');
            redirect('/auth/login');
        }

        // Log in
        Auth::login((int) $user['id']);
        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], ['id' => $user['id']]);

        // 🔔 Notifications
        Notification::ensureTable();
        if ($isNew) {
            // Welcome new student
            Notification::forUser(
                (int) $user['id'],
                '👋 Welcome to The Code Munk!',
                'Your account is ready. Explore courses, join events and build your developer portfolio.',
                '🎉',
                base_url('/student')
            );
            // Alert admins
            Notification::toAdmins(
                '🆕 New Student — ' . $user['name'],
                $user['name'] . ' just joined via Google (' . ($googleUser['email'] ?? '') . ').',
                '🆕',
                base_url('/admin/students')
            );
            FirebaseNotification::notifyAdmins(
                '🆕 New Student — ' . $user['name'],
                $user['name'] . ' just signed up with Google.',
                ['tag' => 'new-student'],
                base_url('/admin/students')
            );
        } else {
            // Welcome back existing student
            if ($user['role'] === 'student') {
                Notification::forUser(
                    (int) $user['id'],
                    '👋 Welcome back, ' . $user['name'] . '!',
                    'Good to see you again. Check what\'s new — courses, events and more await.',
                    '👋',
                    base_url('/student')
                );
            }
        }

        flash('success', 'Welcome, ' . $user['name'] . '!');

        if (!empty($returnTo) && str_starts_with($returnTo, '/') && !str_starts_with($returnTo, '//')) {
            redirect($returnTo);
        }

        // Redirect based on role & onboarding
        if ($user['role'] === 'admin') {
            redirect('/admin');
        }
        if ((int) $user['onboarded'] === 0) {
            redirect('/student/onboarding');
        }
        redirect('/student');
    }

    // ----------------------------------------------------------------- //
    // Private helpers
    // ----------------------------------------------------------------- //

    /** Exchange authorization code for access + id tokens */
    private function exchangeCode(string $code): ?array
    {
        $response = $this->httpPost(self::GOOGLE_TOKEN_URL, [
            'code'          => $code,
            'client_id'     => config('google.client_id'),
            'client_secret' => config('google.client_secret'),
            'redirect_uri'  => config('google.redirect_uri'),
            'grant_type'    => 'authorization_code',
        ]);

        if (isset($response['access_token'])) {
            return $response;
        }
        return null;
    }

    /** Fetch user profile using the access token */
    private function fetchUserInfo(string $accessToken): ?array
    {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . $accessToken,
                'timeout' => 10,
            ],
        ]);

        $body = @file_get_contents(self::GOOGLE_USERINFO_URL, false, $ctx);
        if ($body === false) return null;

        $data = json_decode($body, true);
        if (!isset($data['sub'])) return null;

        return $data;
    }

    /**
     * Find existing user by email or create new student account.
     *
     * @param array<string,mixed> $googleUser
     * @return array<string,mixed>
     */
    private function findOrCreate(array $googleUser): array
    {
        $email = strtolower(trim($googleUser['email'] ?? ''));
        $name  = trim($googleUser['name'] ?? ucfirst(explode('@', $email)[0]));
        $avatar = $googleUser['picture'] ?? null;

        $user = Database::first('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);

        if ($user !== null) {
            // Update avatar if not set
            if (empty($user['avatar']) && $avatar) {
                Database::update('users', ['avatar' => $avatar, 'email_verified' => 1], ['id' => $user['id']]);
                $user['avatar'] = $avatar;
            }
            $user['_is_new'] = false;
            return $user;
        }

        // New user – create student account
        $userId = Database::insert('users', [
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
            'role'          => 'student',
            'status'        => 'active',
            'avatar'        => $avatar,
            'email_verified' => 1,
        ]);

        // Empty student profile for onboarding
        Database::insert('student_profiles', ['user_id' => $userId]);

        $newUser = Database::first('SELECT * FROM users WHERE id = ?', [$userId]);
        $newUser['_is_new'] = true;
        return $newUser;
    }

    /** Simple HTTP POST using file_get_contents (no cURL dependency) */
    private function httpPost(string $url, array $data): array
    {
        $content = http_build_query($data);
        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($content),
                'content' => $content,
                'timeout' => 10,
            ],
        ]);

        $body = @file_get_contents($url, false, $ctx);
        if ($body === false) return [];

        return json_decode($body, true) ?? [];
    }
}
