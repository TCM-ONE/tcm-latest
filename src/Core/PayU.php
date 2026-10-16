<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * PayU India Hosted Checkout Integration Helper
 * Handles SHA-512 cryptographic hash generation, reverse response verification,
 * and hosted checkout form generation.
 */
final class PayU
{
    public const ENDPOINT_PRODUCTION = 'https://secure.payu.in/_payment';
    public const ENDPOINT_SANDBOX    = 'https://test.payu.in/_payment';

    /**
     * Check if PayU is properly configured.
     */
    public static function isConfigured(): bool
    {
        return self::key() !== '' && self::salt() !== '';
    }

    /**
     * Get configured Merchant Key.
     */
    public static function key(): string
    {
        return (string) config('payu.key', '');
    }

    /**
     * Get configured Merchant Salt.
     */
    public static function salt(): string
    {
        return (string) config('payu.salt', '');
    }

    /**
     * Get payment gateway mode (production or sandbox).
     */
    public static function mode(): string
    {
        return strtolower((string) config('payu.mode', 'production'));
    }

    /**
     * Get the PayU hosted checkout URL based on mode.
     */
    public static function endpoint(): string
    {
        return self::mode() === 'sandbox'
            ? self::ENDPOINT_SANDBOX
            : self::ENDPOINT_PRODUCTION;
    }

    /**
     * Generate unique transaction ID.
     */
    public static function generateTxnId(string $prefix = 'TCM'): string
    {
        return sprintf('%s_%s_%s', $prefix, date('YmdHis'), strtoupper(bin2hex(random_bytes(3))));
    }

    /**
     * Calculate SHA-512 request hash for PayU hosted payment form.
     * Formula: sha512(key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt)
     *
     * @param array<string,mixed> $params
     */
    public static function generateRequestHash(array $params): string
    {
        $key         = self::key();
        $salt        = self::salt();
        $txnid       = (string) ($params['txnid'] ?? '');
        $amount      = number_format((float) ($params['amount'] ?? 0), 2, '.', '');
        $productinfo = (string) ($params['productinfo'] ?? '');
        $firstname   = (string) ($params['firstname'] ?? '');
        $email       = (string) ($params['email'] ?? '');
        $udf1        = (string) ($params['udf1'] ?? '');
        $udf2        = (string) ($params['udf2'] ?? '');
        $udf3        = (string) ($params['udf3'] ?? '');
        $udf4        = (string) ($params['udf4'] ?? '');
        $udf5        = (string) ($params['udf5'] ?? '');

        $hashSequence = sprintf(
            '%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s||||||%s',
            $key,
            $txnid,
            $amount,
            $productinfo,
            $firstname,
            $email,
            $udf1,
            $udf2,
            $udf3,
            $udf4,
            $udf5,
            $salt
        );

        return strtolower(hash('sha512', $hashSequence));
    }

    /**
     * Verify PayU response reverse hash.
     * Formula:
     *   With additionalCharges:
     *     sha512(additionalCharges|salt|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)
     *   Without additionalCharges:
     *     sha512(salt|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)
     *
     * @param array<string,mixed> $response
     */
    public static function verifyResponseHash(array $response): bool
    {
        $postedHash = strtolower((string) ($response['hash'] ?? ''));
        if ($postedHash === '') {
            return false;
        }

        $salt              = self::salt();
        $key               = (string) ($response['key'] ?? self::key());
        $status            = (string) ($response['status'] ?? '');
        $txnid             = (string) ($response['txnid'] ?? '');
        $amount            = number_format((float) ($response['amount'] ?? 0), 2, '.', '');
        $productinfo       = (string) ($response['productinfo'] ?? '');
        $firstname         = (string) ($response['firstname'] ?? '');
        $email             = (string) ($response['email'] ?? '');
        $udf1              = (string) ($response['udf1'] ?? '');
        $udf2              = (string) ($response['udf2'] ?? '');
        $udf3              = (string) ($response['udf3'] ?? '');
        $udf4              = (string) ($response['udf4'] ?? '');
        $udf5              = (string) ($response['udf5'] ?? '');
        $additionalCharges = isset($response['additionalCharges']) ? (string) $response['additionalCharges'] : '';

        if ($additionalCharges !== '') {
            $hashSequence = sprintf(
                '%s|%s|%s||||||%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s',
                $additionalCharges,
                $salt,
                $status,
                $udf5,
                $udf4,
                $udf3,
                $udf2,
                $udf1,
                $email,
                $firstname,
                $productinfo,
                $amount,
                $txnid,
                $key
            );
        } else {
            $hashSequence = sprintf(
                '%s|%s||||||%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s',
                $salt,
                $status,
                $udf5,
                $udf4,
                $udf3,
                $udf2,
                $udf1,
                $email,
                $firstname,
                $productinfo,
                $amount,
                $txnid,
                $key
            );
        }

        $calculatedHash = strtolower(hash('sha512', $hashSequence));

        return hash_equals($calculatedHash, $postedHash);
    }

    /**
     * Render a self-submitting HTML form that redirects the user's browser directly to PayU.
     *
     * @param array<string,string> $fields
     */
    public static function renderAutoSubmitForm(array $fields): string
    {
        $endpoint = self::endpoint();
        $inputs = '';
        foreach ($fields as $name => $val) {
            $inputs .= sprintf(
                '  <input type="hidden" name="%s" value="%s">' . "\n",
                htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8')
            );
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connecting to Secure Payment Gateway — TCM One</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: #0A0A0A;
      color: #F5F5F0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .tcm-pay-box {
      background: #141414;
      border: 1px solid rgba(255,255,255,0.1);
      max-width: 440px;
      width: 100%;
      padding: 40px 32px;
      text-align: center;
    }
    .spinner {
      width: 44px;
      height: 44px;
      border: 3px solid rgba(255,255,255,0.15);
      border-top-color: #FFFFFF;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: 0 auto 24px auto;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    h1 { font-size: 20px; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.01em; }
    p { font-size: 13.5px; color: rgba(255,255,255,0.65); line-height: 1.6; margin-bottom: 24px; }
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: rgba(255,255,255,0.5);
      border: 1px solid rgba(255,255,255,0.12);
      padding: 4px 10px;
    }
  </style>
</head>
<body>
  <div class="tcm-pay-box">
    <div class="spinner"></div>
    <h1>Connecting to PayU Secure Gateway</h1>
    <p>Please wait while we redirect you to complete your payment with UPI, Card, or NetBanking. Do not refresh or press back.</p>
    <div class="badge">🔒 256-Bit SSL Encrypted</div>

    <form id="payuForm" method="POST" action="{$endpoint}">
{$inputs}
    </form>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var form = document.getElementById('payuForm');
      if (form) form.submit();
    });
  </script>
</body>
</html>
HTML;
    }

    /**
     * Get PayU PostService endpoint for server-to-server verification.
     */
    public static function postserviceEndpoint(): string
    {
        return self::mode() === 'sandbox'
            ? 'https://test.payu.in/merchant/postservice.php?form=2'
            : 'https://info.payu.in/merchant/postservice.php?form=2';
    }

    /**
     * Server-to-server call to PayU Verify Payment API (verify_payment command).
     * Formula: sha512(key|command|var1|salt)
     *
     * @return array<string,mixed>|null Returns transaction details array on success, null on failure/not found
     */
    public static function verifyPaymentApi(string $txnid): ?array
    {
        $key     = self::key();
        $salt    = self::salt();
        $command = 'verify_payment';
        $var1    = $txnid;

        $hash = strtolower(hash('sha512', sprintf('%s|%s|%s|%s', $key, $command, $var1, $salt)));

        $postData = http_build_query([
            'key'     => $key,
            'command' => $command,
            'var1'    => $var1,
            'hash'    => $hash,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::postserviceEndpoint());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200 || !$response) {
            error_log(sprintf('[PayU::verifyPaymentApi] cURL failure (HTTP %d, Error: %s)', $httpCode, $curlError));
            return null;
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded) || ($decoded['status'] ?? 0) != 1) {
            return null;
        }

        $details = $decoded['transaction_details'][$txnid] ?? null;
        if (!is_array($details)) {
            if (!empty($decoded['transaction_details']) && is_array($decoded['transaction_details'])) {
                $first = reset($decoded['transaction_details']);
                return is_array($first) ? $first : null;
            }
            return null;
        }

        return $details;
    }

    /**
     * Check status of refund/action via PayU PostService (check_action_status command).
     * Formula: sha512(key|command|var1|salt)
     *
     * @return array<string,mixed>|null
     */
    public static function checkActionStatusApi(string $actionId): ?array
    {
        $key     = self::key();
        $salt    = self::salt();
        $command = 'check_action_status';
        $var1    = $actionId;

        $hash = strtolower(hash('sha512', sprintf('%s|%s|%s|%s', $key, $command, $var1, $salt)));

        $postData = http_build_query([
            'key'     => $key,
            'command' => $command,
            'var1'    => $var1,
            'hash'    => $hash,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::postserviceEndpoint());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Sanitize sensitive payment data before persisting in logs.
     * Redacts full card numbers, CVVs, netbanking passwords.
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function sanitizePayload(array $payload): array
    {
        $sanitized = $payload;
        $sensitiveKeys = ['card_number', 'cardnum', 'ccnum', 'cvv', 'cvv2', 'password', 'token', 'client_secret'];

        foreach ($sanitized as $k => $v) {
            $lowerKey = strtolower((string) $k);
            if (in_array($lowerKey, $sensitiveKeys, true)) {
                $sanitized[$k] = '[REDACTED]';
            } elseif (is_string($v) && strlen($v) >= 12 && preg_match('/^\d{12,19}$/', $v)) {
                // Card number pattern: mask all but last 4 digits
                $sanitized[$k] = str_repeat('*', strlen($v) - 4) . substr($v, -4);
            } elseif (is_array($v)) {
                $sanitized[$k] = self::sanitizePayload($v);
            }
        }

        return $sanitized;
    }
}
