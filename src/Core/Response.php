<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * Helpers for emitting JSON API responses.
 */
final class Response
{
    /**
     * @param mixed $data
     */
    public static function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success(mixed $data = null, string $message = 'OK', int $status = 200): never
    {
        self::json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    /**
     * @param array<string,mixed> $errors
     */
    public static function error(string $message, int $status = 400, array $errors = []): never
    {
        self::json(['success' => false, 'message' => $message, 'errors' => $errors], $status);
    }

    public static function redirect(string $path, int $status = 302): never
    {
        $location = str_starts_with($path, 'http') ? $path : base_url($path);
        http_response_code($status);
        header('Location: ' . $location);
        exit;
    }
}
