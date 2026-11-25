<?php

namespace TCM\Services;

final class RegionalPricing
{
    public static function currency(): string
    {
        return match (self::country()) {
            'US' => 'USD',
            'AE' => 'AED',
            default => 'INR',
        };
    }

    private static function country(): string
    {
        // The deployment supplies country from its CDN or GeoIP module.
        return strtoupper($_SERVER['GEOIP_COUNTRY_CODE'] ?? $_SERVER['HTTP_CF_IPCOUNTRY'] ?? '');
    }

    public static function isInternational(): bool
    {
        $country = self::country();
        return !in_array($country, ['IN', 'XX', 'ZZ', 'T1'], true) && preg_match('/^[A-Z]{2}$/D', $country) === 1;
    }

    public static function preventSharedCaching(): void
    {
        header('Cache-Control: private, no-store');
    }

    public static function charge(float $baseInr): float
    {
        if ($baseInr <= 0 || !self::isInternational()) {
            return $baseInr;
        }

        $basePaise = (int) round($baseInr * 100);

        return intdiv($basePaise * 135 + 99, 100) / 100;
    }

    public static function display(float $baseInr): string
    {
        $charge = self::charge($baseInr);
        $currency = self::currency();
        if ($currency === 'INR') {
            return self::formatInr($charge);
        }

        $defaultRate = $currency === 'USD' ? 93.0 : 93.0 / 3.6725;
        $rate = (float) getenv('PRICING_INR_PER_' . $currency);
        if (!is_finite($rate) || $rate <= 0) {
            $rate = $defaultRate;
        }

        $prefix = $currency === 'USD' ? '$' : 'AED ';

        return ($charge > 0 ? '≈ ' : '') . $prefix . number_format($charge / $rate, 2);
    }

    public static function formatInr(float $amount): string
    {
        return '₹' . number_format($amount, round($amount, 2) === round($amount) ? 0 : 2);
    }
}
