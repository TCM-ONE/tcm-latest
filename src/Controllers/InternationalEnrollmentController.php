<?php

declare(strict_types=1);

namespace TCM\Controllers;

use DateTimeImmutable;
use DateTimeZone;
use TCM\Core\Controller;
use TCM\Core\Csrf;
use TCM\Core\Request;
use TCM\Models\Batch;
use TCM\Models\Course;
use TCM\Models\Lead;
use TCM\Models\PaymentSubmission;
use TCM\Services\DiwaliOffer;
use TCM\Services\InternationalEnrollmentQuote;
use TCM\Services\RegionalPricing;

final class InternationalEnrollmentController extends Controller
{
    private const DISCOVERY = ['Google Search', 'ChatGPT', 'Gemini', 'Perplexity', 'Microsoft Copilot', 'Instagram', 'Facebook', 'Other social media', 'Friend or referral', 'Other'];

    public function show(array $params): void
    {
        $this->page($params, false);
    }

    public function prepare(array $params): void
    {
        if (!Csrf::validate(Request::string('_csrf'))) {
            RegionalPricing::preventSharedCaching();
            header('X-Robots-Tag: noindex, nofollow');
            http_response_code(403);
            echo 'Your enrollment session has expired. Reopen the course enrollment page and try again.';
            return;
        }
        $this->page($params, true);
    }

    private function page(array $params, bool $submitted): void
    {
        RegionalPricing::preventSharedCaching();
        header('X-Robots-Tag: noindex, nofollow');
        $course = Course::findBySlug((string) ($params['slug'] ?? ''));
        if (!$course || ($course['status'] ?? '') !== 'published' || (float) $course['price'] <= 0) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Course unavailable'], null);
            return;
        }

        $courseId = (int) $course['id'];
        $zones = DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC);
        $fields = [
            'name' => $submitted ? Request::string('name') : '',
            'email' => $submitted ? strtolower(Request::string('email')) : '',
            'phone' => $submitted ? Request::string('phone') : '',
            'residence' => $submitted ? strtoupper(Request::string('residence')) : (RegionalPricing::isInternational() ? RegionalPricing::country() : ''),
            'billing' => $submitted ? strtoupper(Request::string('billing')) : '',
            'currency' => $submitted ? Request::string('currency') : RegionalPricing::currency(),
            'timezone' => $submitted ? Request::string('timezone') : 'Asia/Kolkata',
            'batch' => $submitted ? Request::string('batch') : '',
            'timing' => $submitted ? Request::string('timing') : '',
            'discovery' => $submitted ? Request::string('discovery') : '',
        ];
        $base = DiwaliOffer::priceFor((string) $course['slug'], (float) $course['price']);
        $category = (int) ($course['category_id'] ?? 0);
        $pricingCurrency = in_array($fields['currency'], RegionalPricing::CURRENCIES, true) ? $fields['currency'] : 'INR';
        $amount = RegionalPricing::internationalCourseCharge($base, $category);
        $batches = [];
        try {
            $availableBatches = Batch::listFor('course', $courseId);
        } catch (\PDOException $exception) {
            if ($exception->getCode() !== '42S02') {
                throw $exception;
            }
            $availableBatches = [];
        }
        foreach ($availableBatches as $batch) {
            if (in_array($batch['status'], ['registration_open', 'upcoming'], true)
                && !empty($batch['starts_at']) && !$batch['is_started'] && !$batch['is_closed'] && !$batch['is_sold_out']) {
                $batches[(int) $batch['id']] = (new DateTimeImmutable($batch['starts_at'], new DateTimeZone('Asia/Kolkata')))->format(DATE_ATOM);
            }
        }
        $errors = [];
        if ($submitted) {
            $phone = preg_replace('/[\s().-]/', '', $fields['phone']);
            $requestTiming = $fields['batch'] === 'request';
            $batchId = ctype_digit($fields['batch']) ? (int) $fields['batch'] : 0;
            if ($fields['name'] === '' || mb_strlen($fields['name']) > 120 || strlen($fields['email']) > 190
                || !filter_var($fields['email'], FILTER_VALIDATE_EMAIL) || !preg_match('/^\+[1-9][0-9]{6,14}$/D', $phone)) {
                $errors[] = 'Enter your name, a valid email, and a phone number with its international calling code.';
            }
            if (!preg_match('/^[A-Z]{2}$/D', $fields['residence']) || !preg_match('/^[A-Z]{2}$/D', $fields['billing'])
                || !in_array($fields['currency'], RegionalPricing::CURRENCIES, true) || !in_array($fields['timezone'], $zones, true)
                || ($fields['discovery'] !== '' && !in_array($fields['discovery'], self::DISCOVERY, true))) {
                $errors[] = 'Check the country codes, display currency, time zone, and discovery answer.';
            }
            if (($requestTiming && ($fields['timing'] === '' || mb_strlen($fields['timing']) > 240))
                || (!$requestTiming && !isset($batches[$batchId]))) {
                $errors[] = 'Select an available cohort or describe your preferred class time.';
            }
            if (Request::string('consent') !== '1') {
                $errors[] = 'Review the policies and confirm before continuing.';
            }
            if (!$requestTiming && Request::string('reviewed_amount') !== number_format($amount, 2, '.', '')) {
                $errors[] = 'The course fee changed. Review the updated INR total before continuing.';
            }
            if (!$requestTiming && ((string) env('INTL_BANK_ACCOUNT', '') === '' || !PaymentSubmission::supportsInternationalDetails())) {
                $errors[] = 'Online bank instructions are unavailable. Contact admissions about payment before sending any funds.';
            }
            if (!$errors) {
                $details = [
                    'Residence' => $fields['residence'], 'Billing country' => $fields['billing'],
                    'Time zone' => $fields['timezone'], 'Currency' => $fields['currency'],
                    'Discovery' => $fields['discovery'], 'Batch ID' => $batchId ?: null,
                    'Cohort start' => $batches[$batchId] ?? null, 'Preferred timing' => $requestTiming ? $fields['timing'] : null,
                    'Catalog INR' => (float) $course['price'], 'Applicable base INR' => $base,
                    'Markup (%)' => round(($amount / $base - 1) * 100, 2),
                    'Payable INR' => $amount,
                ];
                if ($requestTiming) {
                    Lead::capture([
                        'name' => $fields['name'], 'email' => $fields['email'], 'phone' => $phone,
                        'interest_type' => 'course', 'interest_id' => $courseId, 'interest_title' => $course['title'],
                        'source' => 'international-enrollment', 'message' => InternationalEnrollmentQuote::notes($details),
                    ]);
                    flash('success', 'Your preferred timing was sent to admissions. No payment has been requested.');
                    redirect('/enrollment/international/' . rawurlencode($course['slug']));
                }
                $display = RegionalPricing::displayInternationalCourse($base, $category, $pricingCurrency);
                $details['Displayed estimate'] = $display;
                $quote = InternationalEnrollmentQuote::issue(
                    $courseId,
                    $fields['email'],
                    $phone,
                    $amount,
                    $display,
                    $details
                );
                redirect('/student/payments/submit?' . http_build_query(['type' => 'course', 'id' => $courseId, 'quote' => $quote]));
            }
            http_response_code(422);
        }

        $estimates = [];
        $amounts = [];
        foreach (RegionalPricing::CURRENCIES as $currency) {
            $estimates[$currency] = RegionalPricing::displayInternationalCourse($base, $category, $currency);
            $amounts[$currency] = $amount;
        }
        $notice = flash('success');
        $this->view('public/enrollment/international', [
            'course' => $course, 'batches' => $batches, 'zones' => $zones, 'fields' => $fields,
            'errors' => $errors, 'amount' => $amount, 'estimates' => $estimates, 'amounts' => $amounts,
            'discoveryOptions' => self::DISCOVERY, 'notice' => $notice,
            'canTransfer' => (string) env('INTL_BANK_ACCOUNT', '') !== '' && PaymentSubmission::supportsInternationalDetails(),
        ], null);
    }
}
