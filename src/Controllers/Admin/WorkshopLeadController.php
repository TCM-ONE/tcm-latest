<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Core\WhatsApp;
use TCM\Models\WorkshopLead;

final class WorkshopLeadController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');

        $filters = [
            'lead_status'    => Request::string('lead_status') ?: null,
            'workshop_slug'  => Request::string('workshop_slug') ?: null,
            'payment_status' => Request::string('payment_status') ?: null,
            'search'         => Request::string('q') ?: null,
        ];

        $leads = WorkshopLead::search($filters);
        $stats = WorkshopLead::stats();

        // Attach WhatsApp direct conversation link per lead
        foreach ($leads as &$lead) {
            $wsTitle = $lead['workshop_title'] ?: 'Live Workshop';
            $msg = sprintf(
                "Hi %s! Thank you for registering for TCM %s. We have your seat reserved. Do you have any questions before the session begins?",
                $lead['full_name'],
                $wsTitle
            );
            $lead['wa_link'] = !empty($lead['phone']) ? WhatsApp::link($msg, $lead['phone']) : null;
        }

        // Available workshops for filter dropdown
        $workshops = Database::all("SELECT slug, title FROM events WHERE status != 'cancelled' ORDER BY title ASC");

        $this->view('admin/workshop_leads/index', [
            'title'     => 'Workshop Leads',
            'leads'     => $leads,
            'stats'     => $stats,
            'workshops' => $workshops,
            'filters'   => $filters,
        ], 'admin');
    }

    public function updateStatus(array $params): void
    {
        Auth::require('admin');
        $id = (int)$params['id'];
        $status = Request::string('lead_status');

        $allowed = ['new', 'contacted', 'interested', 'payment_pending', 'enrolled', 'dropped'];
        if (in_array($status, $allowed, true)) {
            WorkshopLead::update($id, ['lead_status' => $status]);
        }

        $this->respond(null, 'Lead status updated.', '/admin/workshop-leads');
    }

    public function updatePayment(array $params): void
    {
        Auth::require('admin');
        $id = (int)$params['id'];
        $paymentStatus = Request::string('payment_status');

        $allowed = ['not_initiated', 'pending', 'paid', 'failed', 'refunded'];
        if (in_array($paymentStatus, $allowed, true)) {
            $update = ['payment_status' => $paymentStatus];
            if ($paymentStatus === 'paid') {
                $update['lead_status'] = 'enrolled';
                $update['enrollment_status'] = 'confirmed';
            }
            WorkshopLead::update($id, $update);
        }

        $this->respond(null, 'Payment status updated.', '/admin/workshop-leads');
    }

    public function destroy(array $params): void
    {
        Auth::require('admin');
        $id = (int)$params['id'];
        WorkshopLead::delete($id);
        $this->respond(null, 'Workshop lead deleted.', '/admin/workshop-leads');
    }

    public function exportCsv(): void
    {
        Auth::require('admin');

        $filters = [
            'lead_status'    => Request::string('lead_status') ?: null,
            'workshop_slug'  => Request::string('workshop_slug') ?: null,
            'payment_status' => Request::string('payment_status') ?: null,
            'search'         => Request::string('q') ?: null,
        ];

        $leads = WorkshopLead::search($filters);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="tcm-workshop-leads-' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, [
            'ID', 'Name', 'Email', 'Phone', 'Workshop', 'Price (INR)', 'Current Status',
            'College', 'City', 'Learning Goal', 'Lead Status', 'Payment Status',
            'UTM Source', 'UTM Campaign', 'Date Registered'
        ]);

        foreach ($leads as $l) {
            fputcsv($out, [
                $l['id'],
                $l['full_name'],
                $l['email'],
                $l['phone'],
                $l['workshop_title'],
                $l['selected_workshop_price'],
                $l['current_status'],
                $l['college'],
                $l['city'],
                $l['learning_goal'],
                $l['lead_status'],
                $l['payment_status'],
                $l['utm_source'],
                $l['utm_campaign'],
                $l['created_at'],
            ]);
        }
        fclose($out);
        exit;
    }
}
