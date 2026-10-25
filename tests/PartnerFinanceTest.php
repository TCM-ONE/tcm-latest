<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/Services/PartnerFinance.php';

use TCM\Services\PartnerFinance;

function same(float $expected, float $actual, string $message): void
{
    if (abs($expected - $actual) > 0.001) throw new RuntimeException($message . ': expected ' . $expected . ', got ' . $actual);
}

$academic = PartnerFinance::allocate(100000, 0, 0, 0, 'academic', 40, 60, null);
same(100000, $academic['eligible'], 'Academic eligible base');
same(40000, $academic['partner'], 'Academic partner share');
same(60000, $academic['tcm'], 'Academic TCM share');
$net = PartnerFinance::allocate(10000, 500, 100, 400, 'referral', null, null, 15);
same(9000, $net['eligible'], 'Referral net base');
same(1350, $net['partner'], 'Referral commission');
same(7650, $net['tcm'], 'Referral TCM share');
same(75, PartnerFinance::outstanding(100, 25), 'Actual settlement subtraction');
same(0, PartnerFinance::outstanding(100, 120), 'Outstanding floor');
$threw = false;
try { PartnerFinance::allocate(100, 70, 40, 0, 'academic', 40, 60, null); } catch (InvalidArgumentException) { $threw = true; }
if (!$threw) throw new RuntimeException('Invalid deductions should be rejected.');
echo "Partner finance checks passed.\n";
