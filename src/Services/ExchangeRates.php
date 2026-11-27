<?php

declare(strict_types=1);

namespace TCM\Services;

final class ExchangeRates
{
    private const REFRESH_AFTER = 86400;
    private const RETRY_AFTER = 900;
    private const EXPIRE_AFTER = 604800;
    private const URL = 'https://api.frankfurter.dev/v2/rates?base=INR&quotes=USD,AED,GBP,EUR,CAD,AUD';

    private static ?array $rates = null;

    public static function fromInr(string $currency): ?float
    {
        self::$rates ??= self::load();
        return self::$rates[$currency] ?? null;
    }

    private static function load(): array
    {
        $path = dirname(__DIR__, 2) . '/storage/exchange-rates.json';
        $handle = @fopen($path, 'c+');
        if ($handle === false) {
            return [];
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                return [];
            }

            $cache = json_decode(stream_get_contents($handle), true) ?: [];
            $now = time();
            $updated = (int) ($cache['updated'] ?? 0);
            if ($now - $updated >= self::REFRESH_AFTER && $now - ($cache['attempted'] ?? 0) >= self::RETRY_AFTER) {
                $cache['attempted'] = $now;
                $rates = self::fetch();
                if ($rates !== []) {
                    $cache['rates'] = $rates;
                    $cache['updated'] = $updated = $now;
                }

                rewind($handle);
                ftruncate($handle, 0);
                fwrite($handle, json_encode($cache, JSON_THROW_ON_ERROR));
                fflush($handle);
            }

            return $now - $updated <= self::EXPIRE_AFTER ? ($cache['rates'] ?? []) : [];
        } finally {
            fclose($handle);
        }
    }

    private static function fetch(): array
    {
        if (!function_exists('curl_init')) {
            return [];
        }

        $request = curl_init(self::URL);
        curl_setopt_array($request, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_FAILONERROR => true,
        ]);
        $response = curl_exec($request);
        curl_close($request);
        $rows = is_string($response) ? json_decode($response, true) : null;
        $rates = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            $quote = $row['quote'] ?? '';
            $rate = (float) ($row['rate'] ?? 0);
            if (($row['base'] ?? '') === 'INR' && $quote !== 'INR'
                && in_array($quote, RegionalPricing::CURRENCIES, true) && is_finite($rate) && $rate > 0) {
                $rates[$quote] = $rate;
            }
        }

        return count($rates) === count(RegionalPricing::CURRENCIES) - 1 ? $rates : [];
    }
}
