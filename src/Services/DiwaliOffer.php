<?php

declare(strict_types=1);

namespace TCM\Services;

final class DiwaliOffer
{
    public const STARTS_AT = '2026-10-25 00:00:00';
    public const ENDS_AT = '2026-11-11 00:00:00';
    public const TIMEZONE = 'Asia/Kolkata';

    /** Keep the supplied poster prices for its courses that fall below the new threshold. */
    private const LEGACY_PRICES = [
        'full-stack-web-development' => 5499.0,
        'data-analytics-excel-sql-powerbi' => 3999.0,
        'dsa-problem-solving-java' => 4699.0,
        'ai-tools-for-study-work' => 999.0,
        'full-stack-project-lab-react-node' => 3999.0,
        'frontend-portfolio-builder-html-css-js' => 3499.0,
        'data-analysis-dashboard-sprint-excel-powerbi' => 3499.0,
    ];

    public const MINIMUM_REGULAR_PRICE = 8000.0;
    public const DISCOUNT_PERCENT = 60;

    public static function isActive(?\DateTimeImmutable $now = null): bool
    {
        $zone = new \DateTimeZone(self::TIMEZONE);
        $now ??= new \DateTimeImmutable('now', $zone);
        $start = new \DateTimeImmutable(self::STARTS_AT, $zone);
        $end = new \DateTimeImmutable(self::ENDS_AT, $zone);

        return $now >= $start && $now < $end;
    }

    public static function priceFor(string $slug, float $regularPrice, ?\DateTimeImmutable $now = null): float
    {
        if (!self::isActive($now)) {
            return $regularPrice;
        }

        if ($regularPrice > self::MINIMUM_REGULAR_PRICE) {
            return (float) round($regularPrice * (1 - self::DISCOUNT_PERCENT / 100));
        }

        return self::LEGACY_PRICES[$slug] ?? $regularPrice;
    }

    public static function includes(string $slug, ?float $regularPrice = null): bool
    {
        return ($regularPrice !== null && $regularPrice > self::MINIMUM_REGULAR_PRICE)
            || isset(self::LEGACY_PRICES[$slug]);
    }

    public static function endsAtIso(): string
    {
        return (new \DateTimeImmutable(self::ENDS_AT, new \DateTimeZone(self::TIMEZONE)))->format(DATE_ATOM);
    }

    public static function prices(): array
    {
        return self::LEGACY_PRICES;
    }
}
