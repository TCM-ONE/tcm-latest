<?php

namespace TCM\Services;

final class RegionalPricing
{
    public const CURRENCIES = ['INR', 'USD', 'AED', 'GBP', 'EUR', 'CAD', 'AUD'];
    private const EUROZONE = ['AT', 'BE', 'BG', 'HR', 'CY', 'EE', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PT', 'SK', 'SI', 'ES'];

    public static function currency(): string
    {
        $preferred = $_COOKIE['tcm_currency'] ?? '';
        if (in_array($preferred, self::CURRENCIES, true)) {
            return $preferred;
        }

        return match (self::country()) {
            'AE' => 'AED',
            'GB' => 'GBP',
            'CA' => 'CAD',
            'AU' => 'AUD',
            default => !self::isInternational() ? 'INR' :
                (in_array(self::country(), self::EUROZONE, true) ? 'EUR' : 'USD'),
        };
    }

    public static function country(): string
    {
        // Only enable the header when the origin is reachable exclusively through a trusted proxy.
        $proxyCountry = getenv('PRICING_TRUST_CF_COUNTRY') === '1' ? ($_SERVER['HTTP_CF_IPCOUNTRY'] ?? '') : '';
        return strtoupper(trim($_SERVER['GEOIP_COUNTRY_CODE'] ?? $proxyCountry));
    }

    public static function isInternational(): bool
    {
        $country = self::country();
        return !in_array($country, ['IN', 'XX', 'ZZ', 'T1'], true) && preg_match('/^[A-Z]{2}$/D', $country) === 1;
    }

    public static function allowsNewInstallments(): bool
    {
        // Keep the INR fallback for unknown countries without offering EMI in foreign display currencies.
        return !self::isInternational() && self::currency() === 'INR';
    }

    public static function preventSharedCaching(): void
    {
        header('Cache-Control: private, no-store');
    }

    public static function charge(float $baseInr, int $categoryId = 0): float
    {
        if ($baseInr <= 0 || !self::isInternational()) {
            return $baseInr;
        }

        return self::internationalCharge($baseInr, $categoryId);
    }

    public static function courseCharge(float $baseInr, int $categoryId = 0): float
    {
        return $baseInr > 0 && self::currency() === 'USD'
            ? self::internationalCharge($baseInr, $categoryId)
            : self::charge($baseInr, $categoryId);
    }

    public static function displayCourse(float $baseInr, int $categoryId = 0): string
    {
        return $baseInr > 0 && self::currency() === 'USD'
            ? self::displayInternational($baseInr, $categoryId, 'USD')
            : self::display($baseInr, $categoryId);
    }

    public static function internationalCharge(float $baseInr, int $categoryId = 0): float
    {
        $basePaise = (int) round($baseInr * 100);
        return intdiv($basePaise * (100 + self::internationalMarkup($categoryId)) + 99, 100) / 100;
    }

    public static function internationalMarkup(int $categoryId = 0): int
    {
        $overrides = json_decode(getenv('PRICING_CATEGORY_MARKUPS') ?: '{}', true);
        $categoryMarkup = self::validateMarkup($overrides[$categoryId] ?? null);
        return $categoryMarkup ?? self::validateMarkup(getenv('PRICING_INTERNATIONAL_MARKUP')) ?? 45;
    }

    private static function validateMarkup(mixed $value): ?int
    {
        $markup = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 35, 'max_range' => 55],
        ]);
        return $markup === false ? null : $markup;
    }

    public static function display(float $baseInr, int $categoryId = 0): string
    {
        return $baseInr > 0 && self::isInternational()
            ? self::displayInternational($baseInr, $categoryId, self::currency())
            : self::displayCharge($baseInr);
    }

    public static function displayInternational(float $baseInr, int $categoryId, string $currency): string
    {
        $charge = self::internationalCharge($baseInr, $categoryId);
        if ($baseInr > 0 && $currency === 'USD' && getenv('PRICING_ROUND_USD_ESTIMATES') === '1') {
            $rate = ExchangeRates::fromInr('USD');
            if ($rate !== null) {
                $exact = $charge * $rate;
                $candidate = round(($exact - 9) / 10) * 10 + 9;
                $impliedMarkup = $candidate / ($baseInr * $rate) - 1;
                if ($impliedMarkup >= 0.35 && $impliedMarkup <= 0.55 && abs($candidate - $exact) <= $exact * 0.02) {
                    return '≈ $' . number_format($candidate, 0);
                }
            }
        }

        return self::displayCharge($charge, $currency);
    }

    public static function displayCharge(float $charge, ?string $currency = null): string
    {
        $currency ??= self::currency();
        if ($currency === 'INR') {
            return self::formatInr($charge);
        }

        $rate = ExchangeRates::fromInr($currency);
        if ($charge !== 0.0 && $rate === null) {
            return self::formatInr($charge);
        }

        $prefix = $currency === 'USD' ? '$' : $currency . ' ';
        return ($charge > 0 ? '≈ ' : '') . $prefix . number_format($charge * ($rate ?? 0), 2);
    }

    public static function formatInr(float $amount): string
    {
        return '₹' . number_format($amount, round($amount, 2) === round($amount) ? 0 : 2);
    }
}
