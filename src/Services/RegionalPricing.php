<?php

namespace TCM\Services;

final class RegionalPricing
{
    public const CURRENCIES = ['INR', 'USD', 'AED', 'GBP', 'EUR', 'CAD', 'AUD'];
    private const USD_COURSE_MARKUP = 30;
    private const COURSE_MARKETS = [
        'US' => ['currency' => 'USD', 'inr_per_unit' => 93, 'target' => 35, 'prefix' => 'US$', 'prices' => [
            999 => 15, 1499 => 22, 1999 => 30, 2499 => 37, 2999 => 44, 3499 => 51, 4999 => 89,
            5999 => 89, 6999 => 102, 7999 => 119, 8999 => 131, 9999 => 149, 12999 => 189, 14999 => 219,
        ]],
        'AU' => ['currency' => 'AUD', 'inr_per_unit' => 62, 'target' => 40, 'prefix' => 'A$', 'prices' => [
            999 => 22, 1499 => 33, 1999 => 45, 2499 => 56, 2999 => 67, 3499 => 79, 4999 => 112,
            5999 => 135, 6999 => 158, 7999 => 179, 8999 => 199, 9999 => 225, 12999 => 289, 14999 => 338,
        ]],
    ];
    private const EUROZONE = ['AT', 'BE', 'BG', 'HR', 'CY', 'EE', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PT', 'SK', 'SI', 'ES'];

    public static function currency(): string
    {
        $preferred = $_COOKIE['tcm_currency'] ?? '';
        if (in_array($preferred, self::CURRENCIES, true)) {
            return $preferred;
        }

        return self::regionalCurrency();
    }

    private static function regionalCurrency(): string
    {
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
        $country = self::normalizeCountry($_SERVER['GEOIP_COUNTRY_CODE'] ?? '');
        // Only enable the header when the origin is reachable exclusively through a trusted proxy.
        if ($country === '' && getenv('PRICING_TRUST_CF_COUNTRY') === '1') {
            $country = self::normalizeCountry($_SERVER['HTTP_CF_IPCOUNTRY'] ?? '');
        }

        // Local development has no GeoIP result and uses the India checkout.
        if ($country === '' && getenv('APP_ENV') === 'development'
            && in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
            return 'IN';
        }

        return $country;
    }

    private static function normalizeCountry(string $value): string
    {
        $country = strtoupper(trim($value));
        return preg_match('/^[A-Z]{2}$/D', $country) === 1 && !in_array($country, ['XX', 'ZZ'], true)
            ? $country
            : '';
    }

    public static function isInternational(): bool
    {
        return !in_array(self::country(), ['', 'IN'], true);
    }

    public static function allowsNewInstallments(): bool
    {
        return self::country() === 'IN' && self::currency() === 'INR';
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
        return $baseInr > 0 && self::isInternational()
            ? self::internationalCourseCharge($baseInr, $categoryId)
            : $baseInr;
    }

    public static function displayCourse(float $baseInr, int $categoryId = 0): string
    {
        return $baseInr > 0 && self::isInternational()
            ? self::displayInternationalCourse($baseInr, $categoryId, self::currency())
            : self::displayCharge($baseInr);
    }

    public static function internationalCharge(float $baseInr, int $categoryId = 0): float
    {
        return self::applyMarkup($baseInr, self::internationalMarkup($categoryId));
    }

    public static function internationalCourseCharge(float $baseInr, int $categoryId): float
    {
        if ($baseInr <= 0) {
            return $baseInr;
        }

        $market = self::COURSE_MARKETS[self::country()] ?? null;
        if ($market !== null) {
            return round(self::marketCoursePrice($baseInr, $market) * $market['inr_per_unit'], 2);
        }

        $markup = self::regionalCurrency() === 'USD' ? self::USD_COURSE_MARKUP : self::internationalMarkup($categoryId);
        return self::applyMarkup($baseInr, $markup);
    }

    private static function marketCoursePrice(float $baseInr, array $market): float
    {
        $converted = $baseInr / $market['inr_per_unit'];
        if ($baseInr === (float) (int) $baseInr && isset($market['prices'][(int) $baseInr])) {
            return $market['prices'][(int) $baseInr];
        }

        $minimum = ceil($converted * 1.35);
        $maximum = floor($converted * 1.40);
        $price = $minimum <= $maximum
            ? ($market['target'] === 35 ? $minimum : $maximum)
            : round($converted * (1 + $market['target'] / 100), 2);

        // Keep off-tier prices in order around the published price ladder.
        foreach ($market['prices'] as $tierInr => $tierPrice) {
            if ($baseInr < $tierInr) {
                return min($price, $tierPrice);
            }
            $price = max($price, $tierPrice);
        }
        return $price;
    }

    public static function displayInternationalCourse(float $baseInr, int $categoryId, string $currency): string
    {
        $market = self::COURSE_MARKETS[self::country()] ?? null;
        if ($baseInr > 0 && $market !== null && $currency === $market['currency']) {
            $price = self::marketCoursePrice($baseInr, $market);
            return $market['prefix'] . number_format($price, floor($price) === $price ? 0 : 2);
        }

        return self::displayCharge(self::internationalCourseCharge($baseInr, $categoryId), $currency);
    }

    private static function applyMarkup(float $baseInr, int $markup): float
    {
        $basePaise = (int) round($baseInr * 100);
        return intdiv($basePaise * (100 + $markup) + 99, 100) / 100;
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
