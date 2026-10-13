<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Core\Validator;
use TCM\Core\WhatsApp;
use TCM\Models\PartnerApplication;

final class PartnerController extends Controller
{
    /**
     * POST /api/partner/apply
     * Submit institutional partnership application.
     */
    public function apply(): void
    {
        $data = Request::all();

        $validator = Validator::make($data, [
            'institution_name'  => 'required|min:2|max:255',
            'contact_person'    => 'required|min:2|max:150',
            'phone'             => 'required|min:8|max:20',
            'email'             => 'required|email|max:190',
            'city'              => 'required|min:2|max:100',
            'partnership_model' => 'required',
        ]);

        if ($validator->fails()) {
            Response::error('Please fill in all required fields correctly.', 422, $validator->errors());
            return;
        }

        // Prevent rapid duplicate submissions within 1 hour
        $email = trim((string) $data['email']);
        $phone = trim((string) $data['phone']);
        PartnerApplication::ensureTables();

        $recent = Database::first(
            "SELECT id, created_at FROM partner_applications 
             WHERE (email = ? OR phone = ?) AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) 
             LIMIT 1",
            [$email, $phone]
        );

        if ($recent) {
            $refCode = 'TCM-PTR-' . str_pad((string) $recent['id'], 5, '0', STR_PAD_LEFT);
            $waMessage = "Hello TCM One Team! We have submitted our Partnership Application (Ref: {$refCode}) for {$data['institution_name']}. Please connect with us.";
            $waUrl = WhatsApp::link($waMessage);

            Response::success([
                'ref_code'     => $refCode,
                'whatsapp_url' => $waUrl,
                'is_duplicate' => true,
            ], 'Your application has already been received recently. Our partnership team will contact you shortly.');
            return;
        }

        $ip = Request::ip();
        $payload = array_merge($data, [
            'ip_address' => $ip,
        ]);

        $res = PartnerApplication::submit($payload);
        $refCode = $res['ref_code'];

        $waMsg = "Hello TCM One Partnership Team! I have submitted an institutional partnership application for *{$data['institution_name']}* in {$data['city']}.\n"
               . "Reference ID: *{$refCode}*\n"
               . "Model: " . ($data['partnership_model'] ?? 'Academic Partnership') . "\n"
               . "Contact Person: {$data['contact_person']}\n"
               . "Looking forward to discussing the next steps!";
        $waUrl = WhatsApp::link($waMsg);

        Response::success([
            'ref_code'     => $refCode,
            'whatsapp_url' => $waUrl,
        ], 'Thank you! Your partnership application has been submitted successfully.');
    }

    /**
     * POST /api/partner/meeting
     * Request a partnership consultation / discovery meeting.
     */
    public function bookMeeting(): void
    {
        $data = Request::all();

        $validator = Validator::make($data, [
            'institution_name' => 'required|min:2|max:255',
            'contact_person'   => 'required|min:2|max:150',
            'phone'            => 'required|min:8|max:20',
            'email'            => 'required|email|max:190',
            'preferred_date'   => 'required',
            'preferred_time'   => 'required',
        ]);

        if ($validator->fails()) {
            Response::error('Please provide institution name, contact details, date and time slot.', 422, $validator->errors());
            return;
        }

        $date = trim((string) $data['preferred_date']);
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            Response::error('Please choose an upcoming business date.', 422, ['preferred_date' => ['Date must be in the future.']]);
            return;
        }

        $ip = Request::ip();
        $payload = array_merge($data, ['ip_address' => $ip]);

        $res = PartnerApplication::bookMeeting($payload);
        $refCode = $res['ref_code'];

        $waMsg = "Hello TCM One! I have requested a partnership consultation meeting for *{$data['institution_name']}* on {$date} ({$data['preferred_time']}).\n"
               . "Reference: *{$refCode}*\n"
               . "Contact: {$data['contact_person']}";
        $waUrl = WhatsApp::link($waMsg);

        Response::success([
            'ref_code'       => $refCode,
            'preferred_date' => $date,
            'preferred_time' => $data['preferred_time'],
            'whatsapp_url'   => $waUrl,
        ], 'Consultation call requested successfully. Our institutional director will confirm your schedule.');
    }

    /**
     * GET /download-partner-proposal
     * Stream the official TCM One Institutional Partnership Proposal PDF.
     */
    public function downloadProposal(): void
    {
        $filePath = BASE_PATH . '/assets/tcm-partnership-proposal.pdf';

        if (!is_file($filePath)) {
            flash('info', 'Proposal document is being updated. You can connect with our partnership team directly!');
            redirect('/partner.html');
            return;
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="TCM-One-Institutional-Partnership-Proposal.pdf"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
