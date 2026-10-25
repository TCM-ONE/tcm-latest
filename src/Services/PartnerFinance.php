<?php

declare(strict_types=1);

namespace TCM\Services;

use InvalidArgumentException;

/** Pure partner revenue allocation rules, separated for repeatable verification. */
final class PartnerFinance
{
    /** @return array{eligible:float,partner:float,tcm:float} */
    public static function allocate(float $gross, float $tax, float $gatewayFee, float $refund, string $model, ?float $partnerShare, ?float $tcmShare, ?float $referralRate): array
    {
        if ($gross < 0 || $tax < 0 || $gatewayFee < 0 || $refund < 0 || $tax + $gatewayFee + $refund > $gross) {
            throw new InvalidArgumentException('Invalid payment deductions.');
        }
        if ($model === 'referral') {
            $p = $referralRate ?? 15.0;
            $t = 100.0 - $p;
        } else {
            $p = $partnerShare ?? ($model === 'academic' ? 40.0 : -1.0);
            $t = $tcmShare ?? ($model === 'academic' ? 60.0 : -1.0);
        }
        if ($p < 0 || $t < 0 || $p > 100 || $t > 100 || abs(($p + $t) - 100.0) > 0.01) {
            throw new InvalidArgumentException('Agreement shares must be configured and total 100%.');
        }
        $base = round($gross - $tax - $gatewayFee - $refund, 2);
        return ['eligible' => $base, 'partner' => round($base * $p / 100, 2), 'tcm' => round($base * $t / 100, 2)];
    }

    public static function outstanding(float $entitlement, float $actualSettlements): float
    {
        return round(max(0.0, $entitlement - $actualSettlements), 2);
    }
}
