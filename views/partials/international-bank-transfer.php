<?php
use TCM\Services\RegionalPricing;

$account = (string) env('INTL_BANK_ACCOUNT', '');
$swift = (string) env('INTL_BANK_SWIFT', '');
?>
<?php if ($account === ''): ?>
    <p>Contact admissions for bank transfer instructions before sending payment.</p>
<?php else: ?>
    <p>The beneficiary must receive <strong><?= e(RegionalPricing::formatInr($prePrice)) ?> INR</strong>. Ask your transfer provider to confirm the amount received after fees.</p>
    <dl>
        <dt>Account holder</dt><dd><?= e(env('INTL_BANK_HOLDER', '')) ?></dd>
        <dt>Bank</dt><dd><?= e(env('INTL_BANK_NAME', '')) ?></dd>
        <dt>Account number</dt><dd><?= e($account) ?></dd>
        <dt>IFSC</dt><dd><?= e(env('INTL_BANK_IFSC', '')) ?></dd>
        <dt>Address</dt><dd><?= e(env('INTL_BANK_ADDRESS', '')) ?></dd>
        <?php if ($swift !== ''): ?>
            <dt>SWIFT/BIC</dt><dd><?= e($swift) ?></dd>
        <?php endif; ?>
    </dl>
    <p>For a SWIFT wire, confirm the routing and any intermediary bank details with Central Bank of India before sending. <a href="https://centralbank.bank.in/en/remittance" target="_blank" rel="noopener noreferrer">Bank remittance instructions</a></p>
<?php endif; ?>
<p>After transferring, upload your receipt and enter the transfer reference. Enrollment is activated after payment verification.</p>
