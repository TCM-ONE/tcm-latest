<?php

declare(strict_types=1);

namespace TCM\Services;

final class InternationalEnrollmentQuote
{
    public static function issue(int $courseId, string $email, string $phone, float $amount, string $display, array $details): string
    {
        $token = bin2hex(random_bytes(24));
        $_SESSION['international_enrollment_quote'] = [
            'token' => $token, 'course' => $courseId, 'email' => $email, 'phone' => $phone,
            'amount' => $amount, 'display' => $display, 'details' => $details, 'expires' => time() + 86400,
        ];
        return $token;
    }

    public static function find(string $token, int $courseId, string $email): ?array
    {
        $quote = $_SESSION['international_enrollment_quote'] ?? null;
        if ($token === '' || !$quote || !hash_equals($quote['token'], $token)
            || $quote['course'] !== $courseId || $quote['email'] !== strtolower($email) || $quote['expires'] < time()) {
            return null;
        }
        return $quote;
    }

    public static function notes(array $details): string
    {
        $lines = [];
        foreach ($details as $label => $value) {
            if ($value !== null && $value !== '') {
                $lines[] = $label . ': ' . $value;
            }
        }
        return implode("\n", $lines);
    }
}
