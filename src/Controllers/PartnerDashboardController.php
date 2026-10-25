<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\PartnerAccess;
use TCM\Core\Request;
use TCM\Core\Mailer;

final class PartnerDashboardController extends Controller
{
    public function dashboard(): void
    {
        $ctx = PartnerAccess::requirePartner();
        $partnerId = (int) $ctx['partner']['id'];
        $stats = [
            'students' => (int) Database::scalar('SELECT COUNT(*) FROM partner_student_attributions WHERE partner_id = ? AND status NOT IN (\'rejected\',\'dropped\')', [$partnerId]),
            'batches' => (int) Database::scalar('SELECT COUNT(*) FROM partner_batches WHERE partner_id = ? AND status IN (\'admissions_open\',\'upcoming\',\'active\')', [$partnerId]),
            'collections' => (float) Database::scalar('SELECT COALESCE(SUM(eligible_amount),0) FROM partner_ledger_entries WHERE partner_id = ? AND status = \'verified\'', [$partnerId]),
            'pending_requests' => (int) Database::scalar('SELECT COUNT(*) FROM partner_service_requests WHERE partner_id = ? AND status NOT IN (\'resolved\',\'closed\',\'completed\',\'cancelled\',\'rejected\')', [$partnerId]),
        ];
        $finance = null;
        if ($ctx['membership']['member_role'] === 'partner_owner' || (int) $ctx['membership']['can_view_finance'] === 1) {
            $finance = $this->financeTotals($partnerId);
        }
        $activity = Database::all('SELECT action, details, created_at FROM partner_activity_logs WHERE partner_id = ? ORDER BY id DESC LIMIT 8', [$partnerId]);
        $this->view('partner/dashboard', ['title' => 'Partner Overview', 'ctx' => $ctx, 'stats' => $stats, 'finance' => $finance, 'activity' => $activity], 'partner');
    }

    public function students(): void
    {
        $ctx = PartnerAccess::requirePartner();
        $rows = Database::all('SELECT a.*, u.name, u.email, u.phone, c.title AS course_title, b.batch_name
            FROM partner_student_attributions a JOIN users u ON u.id = a.student_user_id
            LEFT JOIN courses c ON c.id = a.course_id LEFT JOIN partner_batches b ON b.id = a.batch_id
            WHERE a.partner_id = ? ORDER BY a.created_at DESC', [(int) $ctx['partner']['id']]);
        $courses = Database::all("SELECT id,title FROM courses WHERE status = 'published' ORDER BY title");
        $batches = Database::all('SELECT id,batch_name FROM partner_batches WHERE partner_id = ? AND status <> \'cancelled\' ORDER BY id DESC', [(int) $ctx['partner']['id']]);
        $this->view('partner/students', ['title' => 'Students', 'ctx' => $ctx, 'rows' => $rows, 'courses' => $courses, 'batches' => $batches], 'partner');
    }

    public function addStudent(): void
    {
        $ctx = PartnerAccess::requirePartner(false, true);
        $name = Request::string('name');
        $email = strtolower(Request::string('email'));
        $phone = Request::string('phone');
        $courseId = Request::int('course_id') ?: null;
        $batchId = Request::int('batch_id') ?: null;
        if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Enter the student name and a valid email address.');
            redirect('/partner/students');
        }
        $partnerId = (int) $ctx['partner']['id'];
        if ($batchId && !Database::first('SELECT id FROM partner_batches WHERE id = ? AND partner_id = ?', [$batchId, $partnerId])) {
            http_response_code(403); exit('Invalid batch access.');
        }
        if ($courseId && !Database::first("SELECT id FROM courses WHERE id = ? AND status = 'published'", [$courseId])) {
            flash('error', 'Choose an available TCM One course.'); redirect('/partner/students');
        }
        $student = Database::first('SELECT id,role FROM users WHERE email = ? LIMIT 1', [$email]);
        if ($student && $student['role'] !== 'student') {
            flash('error', 'That email is already used by a non-student account.'); redirect('/partner/students');
        }
        Database::transaction(function () use ($student, $name, $email, $phone, $courseId, $batchId, $partnerId, $ctx): void {
            if ($student === null) {
                $studentId = Auth::registerStudent($name, $email, bin2hex(random_bytes(24)));
                if ($phone !== '') Database::update('users', ['phone' => $phone], ['id' => $studentId]);
            } else {
                $studentId = (int) $student['id'];
                if ($phone !== '') Database::run('UPDATE users SET phone = COALESCE(NULLIF(phone, \'\'), ?) WHERE id = ?', [$phone, $studentId]);
            }
            Database::run('INSERT INTO partner_student_attributions (partner_id,student_user_id,batch_id,course_id,status,consent_at,created_by)
                VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE batch_id = VALUES(batch_id), status = \'pending_review\', consent_at = VALUES(consent_at), updated_at = NOW()',
                [$partnerId, $studentId, $batchId, $courseId, 'pending_review', Request::int('consent') === 1 ? date('Y-m-d H:i:s') : null, $ctx['user']['id']]);
            Database::insert('partner_activity_logs', ['partner_id' => $partnerId, 'user_id' => $ctx['user']['id'], 'action' => 'student_submitted', 'details' => json_encode(['student_user_id' => $studentId])]);
        });
        flash('success', 'Student record submitted for review. It is not counted as a verified enrollment or payment.');
        redirect('/partner/students');
    }

    public function batches(): void
    {
        $ctx = PartnerAccess::requirePartner();
        $partnerId = (int) $ctx['partner']['id'];
        $rows = Database::all('SELECT b.*, c.title AS course_title, u.name AS approver_name FROM partner_batches b LEFT JOIN courses c ON c.id = b.course_id LEFT JOIN users u ON u.id = b.approved_by WHERE b.partner_id = ? ORDER BY b.id DESC', [$partnerId]);
        $courses = Database::all("SELECT id,title FROM courses WHERE status = 'published' ORDER BY title");
        $this->view('partner/batches', ['title' => 'Batches', 'ctx' => $ctx, 'rows' => $rows, 'courses' => $courses], 'partner');
    }

    public function requestBatch(): void
    {
        $ctx = PartnerAccess::requirePartner(false, true);
        $name = Request::string('batch_name');
        if ($name === '') { flash('error', 'Batch name is required.'); redirect('/partner/batches'); }
        $partnerId = (int) $ctx['partner']['id'];
        $courseId = Request::int('course_id') ?: null;
        $start=Request::string('starts_on'); $end=Request::string('ends_on'); $fee=Request::string('fee'); $capacity=Request::int('capacity');
        if($courseId&&!Database::first("SELECT id FROM courses WHERE id=? AND status='published'",[$courseId])){flash('error','Choose an available TCM One course.');redirect('/partner/batches');}
        if($start!==''&&strtotime($start)===false){flash('error','Enter a valid start date.');redirect('/partner/batches');}
        if($end!==''&&strtotime($end)===false){flash('error','Enter a valid end date.');redirect('/partner/batches');}
        if($start!==''&&$end!==''&&$end<$start){flash('error','Batch end date must be on or after its start date.');redirect('/partner/batches');}
        if(($fee!==''&&(!is_numeric($fee)||(float)$fee<0))||$capacity<0){flash('error','Capacity and fee must be zero or greater.');redirect('/partner/batches');}
        $id = Database::insert('partner_batches', [
            'partner_id' => $partnerId, 'course_id' => $courseId, 'batch_name' => $name,
            'starts_on' => $start ?: null, 'ends_on' => $end ?: null,
            'schedule' => Request::string('schedule') ?: null, 'delivery_mode' => Request::string('delivery_mode') ?: null,
            'capacity' => $capacity, 'location' => Request::string('location') ?: null,
            'fee' => $fee!=='' ? $fee : null,
            'status' => 'awaiting_approval', 'created_by' => $ctx['user']['id'],
        ]);
        $this->activity($partnerId, $ctx['user']['id'], 'batch_requested', ['batch_id' => $id]);
        flash('success', 'Batch request sent for approval.'); redirect('/partner/batches');
    }

    public function courses(): void
    {
        $ctx = PartnerAccess::requirePartner();
        $rows = Database::all('SELECT * FROM partner_courses WHERE partner_id = ? ORDER BY id DESC', [(int) $ctx['partner']['id']]);
        $this->view('partner/courses', ['title' => 'Independent Courses', 'ctx' => $ctx, 'rows' => $rows], 'partner');
    }

    public function addCourse(): void
    {
        $ctx = PartnerAccess::requirePartner(false, true);
        $title = Request::string('title');
        if (mb_strlen($title) < 2) { flash('error', 'Course name is required.'); redirect('/partner/courses'); }
        foreach(['original_fee','current_fee'] as $field){$value=Request::string($field);if($value!==''&&(!is_numeric($value)||(float)$value<0)){flash('error','Course fees must be valid non-negative amounts.');redirect('/partner/courses');}}
        $slug = trim((string) preg_replace('/[^a-z0-9]+/i', '-', strtolower($title)), '-');
        $baseSlug = $slug;
        $n = 2;
        while (Database::first('SELECT id FROM partner_courses WHERE partner_id = ? AND slug = ?', [(int) $ctx['partner']['id'], $slug])) $slug = $baseSlug . '-' . $n++;
        Database::insert('partner_courses', [
            'partner_id' => $ctx['partner']['id'], 'title' => $title, 'slug' => $slug,
            'category' => Request::string('category') ?: null, 'description' => Request::string('description') ?: null,
            'duration' => Request::string('duration') ?: null,
            'original_fee' => is_numeric(Request::string('original_fee')) ? Request::string('original_fee') : null,
            'current_fee' => is_numeric(Request::string('current_fee')) ? Request::string('current_fee') : null,
            'delivery_mode' => Request::string('delivery_mode') ?: null, 'eligibility' => Request::string('eligibility') ?: null,
            'certificate_provider' => Request::string('certificate_provider') ?: null, 'review_status' => 'pending_review',
        ]);
        $this->activity((int) $ctx['partner']['id'], (int) $ctx['user']['id'], 'course_submitted', ['title' => $title]);
        flash('success', 'Independent partner course submitted for review.'); redirect('/partner/courses');
    }

    public function finance(): void
    {
        $ctx = PartnerAccess::requirePartner(true, true);
        $partnerId = (int) $ctx['partner']['id'];
        $entries = Database::all('SELECT l.*, u.name AS student_name, b.batch_name FROM partner_ledger_entries l LEFT JOIN users u ON u.id = l.student_user_id LEFT JOIN partner_batches b ON b.id = l.batch_id WHERE l.partner_id = ? ORDER BY l.id DESC LIMIT 200', [$partnerId]);
        $settlements = Database::all('SELECT * FROM partner_settlements WHERE partner_id = ? ORDER BY id DESC LIMIT 100', [$partnerId]);
        $this->view('partner/finance', ['title' => 'Finance & Ledger', 'ctx' => $ctx, 'totals' => $this->financeTotals($partnerId), 'entries' => $entries, 'settlements' => $settlements], 'partner');
    }

    public function requestPage(): void
    {
        $ctx = PartnerAccess::requirePartner();
        $type = Request::string('type');
        if ($type === '') {
            $type = match (Request::path()) {
                '/partner/certifications' => 'certificate',
                '/partner/mentor' => 'mentor',
                '/partner/visits' => 'visit',
                default => 'support',
            };
        }
        $allowed = ['certificate','mentor','visit','support'];
        if (!in_array($type, $allowed, true)) $type = 'support';
        $rows = Database::all('SELECT * FROM partner_service_requests WHERE partner_id = ? AND request_type = ? ORDER BY id DESC', [(int) $ctx['partner']['id'], $type]);
        $students = Database::all('SELECT a.student_user_id,u.name FROM partner_student_attributions a JOIN users u ON u.id=a.student_user_id WHERE a.partner_id=? ORDER BY u.name', [(int) $ctx['partner']['id']]);
        $this->view('partner/requests', ['title' => ucfirst($type) . ' Requests', 'type' => $type, 'ctx' => $ctx, 'rows' => $rows, 'students' => $students], 'partner');
    }

    public function createRequest(): void
    {
        $ctx = PartnerAccess::requirePartner(false, true);
        $type = Request::string('type');
        if (!in_array($type, ['certificate','mentor','visit','support'], true)) { http_response_code(400); exit('Invalid request type.'); }
        if ($type === 'certificate' && Request::int('student_id') > 0 && !Database::first('SELECT id FROM partner_student_attributions WHERE partner_id=? AND student_user_id=?', [(int)$ctx['partner']['id'], Request::int('student_id')])) {
            http_response_code(403); exit('Student is outside this institution.');
        }
        $subject = Request::string('subject');
        $details = Request::string('details');
        if ($subject === '' || $details === '') { flash('error', 'Add a subject and a short description.'); redirect('/partner/' . $type); }
        $payload = Request::all();
        unset($payload['subject'], $payload['details'], $payload['type']);
        Database::insert('partner_service_requests', [
            'partner_id' => $ctx['partner']['id'], 'user_id' => $ctx['user']['id'], 'request_type' => $type,
            'subject' => $subject, 'details' => $details, 'payload' => json_encode($payload),
            'priority' => in_array(Request::string('priority'), ['low','normal','high','urgent'], true) ? Request::string('priority') : 'normal',
            'status' => $type === 'support' ? 'open' : 'submitted',
        ]);
        $this->activity((int) $ctx['partner']['id'], (int) $ctx['user']['id'], $type . '_requested', ['subject' => $subject]);
        $page = $type === 'certificate' ? 'certifications' : ($type === 'mentor' ? 'mentor' : ($type === 'visit' ? 'visits' : 'support'));
        flash('success', 'Request submitted. You can track its status here.'); redirect('/partner/' . $page);
    }

    public function profile(): void
    {
        $ctx = PartnerAccess::requirePartner();
        $this->view('partner/profile', ['title' => 'Institution Profile', 'ctx' => $ctx], 'partner');
    }

    public function updateProfile(): void
    {
        $ctx = PartnerAccess::requirePartner();
        $id = (int) $ctx['partner']['id'];
        $data = [];
        foreach (['display_name','description','city','state','postal_code','address','website','public_email','public_phone','whatsapp','map_url'] as $field) {
            $data[$field] = Request::string($field) ?: null;
        }
        if (($data['display_name'] ?? '') === '') { flash('error','Institution display name is required.'); redirect('/partner/profile'); }
        if (isset($data['map_url']) && $data['map_url'] !== null) {
            $scheme = strtolower((string) parse_url((string)$data['map_url'], PHP_URL_SCHEME));
            if (!filter_var($data['map_url'], FILTER_VALIDATE_URL) || !in_array($scheme,['http','https'],true)) $data['map_url'] = null;
        }
        $data['public_status'] = 'pending_review';
        Database::update('partners', $data, ['id' => $id]);
        $this->activity($id, (int) $ctx['user']['id'], 'profile_submitted', []);
        flash('success', 'Profile changes submitted for public review.'); redirect('/partner/profile');
    }

    public function invite(): void
    {
        $ctx = PartnerAccess::requirePartner(false, true);
        if ($ctx['membership']['member_role'] !== 'partner_owner') { http_response_code(403); exit('Owner access required.'); }
        $email = strtolower(Request::string('email')); $name = Request::string('name');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $name === '') { flash('error', 'Enter a name and valid email.'); redirect('/partner/settings'); }
        $user = Database::first('SELECT id FROM users WHERE email = ?', [$email]);
        if ($user && Database::first('SELECT id FROM partner_memberships WHERE user_id=?',[$user['id']])) { flash('error', 'That account already has partner access.'); redirect('/partner/settings'); }
        $token = bin2hex(random_bytes(32));
        Database::insert('partner_invitations', ['partner_id' => $ctx['partner']['id'], 'email' => $email, 'name' => $name, 'role' => 'partner_staff', 'token_hash' => hash('sha256', $token), 'expires_at' => date('Y-m-d H:i:s', strtotime('+72 hours')), 'created_by' => $ctx['user']['id']]);
        $url = base_url('/partner/invitation/' . $token);
        $sent = Mailer::send($email, 'You are invited to TCM One Partner Portal', '<p>Hello ' . e($name) . ',</p><p><a href="' . e($url) . '">Accept your TCM One partner invitation</a>. This link expires in 72 hours.</p>');
        flash($sent ? 'success' : 'error', $sent ? 'Invitation sent.' : 'Invitation was saved, but email delivery is unavailable. Check the mail configuration before inviting again.');
        redirect('/partner/settings');
    }

    public function settings(): void
    {
        $ctx = PartnerAccess::requirePartner();
        $members = Database::all('SELECT u.id AS user_id,pm.id AS membership_id,u.name,u.email,pm.title,pm.member_role,pm.can_view_finance,pm.status FROM partner_memberships pm JOIN users u ON u.id=pm.user_id WHERE pm.partner_id=? ORDER BY pm.id', [(int) $ctx['partner']['id']]);
        $this->view('partner/settings', ['title' => 'Settings & Team', 'ctx' => $ctx, 'members' => $members], 'partner');
    }

    public function updateMember(array $params): void
    {
        $ctx=PartnerAccess::requirePartner(false,true);
        if($ctx['membership']['member_role']!=='partner_owner'){http_response_code(403);exit('Owner access required.');}
        $memberId=(int)$params['id']; $membership=Database::first('SELECT id FROM partner_memberships WHERE id=? AND partner_id=?',[$memberId,(int)$ctx['partner']['id']]);
        if(!$membership){http_response_code(404);exit('Team member not found.');}
        $status=in_array(Request::string('status'),['active','suspended'],true)?Request::string('status'):'active';
        Database::update('partner_memberships',['can_view_finance'=>Request::int('can_view_finance')===1?1:0,'status'=>$status],['id'=>$memberId]);
        flash('success','Team access updated.');redirect('/partner/settings');
    }

    public function changePassword(): void
    {
        $ctx=PartnerAccess::requirePartner();
        $current=Request::string('current_password'); $next=Request::string('new_password');
        if(!password_verify($current,$ctx['user']['password_hash'])){flash('error','Current password is incorrect.');redirect('/partner/settings');}
        if(strlen($next)<10||$next!==Request::string('new_password_confirm')){flash('error','Choose a password of at least 10 characters and confirm it.');redirect('/partner/settings');}
        Database::update('users',['password_hash'=>password_hash($next,PASSWORD_DEFAULT)],['id'=>$ctx['user']['id']]);
        flash('success','Your TCM One password has been updated.');redirect('/partner/settings');
    }

    public function acceptInvitation(array $params): void
    {
        $token = (string) ($params['token'] ?? '');
        $invite = Database::first('SELECT i.*,p.display_name FROM partner_invitations i JOIN partners p ON p.id=i.partner_id WHERE i.token_hash=? AND i.accepted_at IS NULL AND i.expires_at>NOW()', [hash('sha256', $token)]);
        if (!$invite) { http_response_code(404); echo 'Invitation expired or already used.'; return; }
        $existing=Database::first('SELECT id FROM users WHERE email=?',[$invite['email']]);
        $this->view('partner/accept-invitation', ['title' => 'Accept Partner Invitation', 'invite' => $invite, 'token' => $token, 'existingAccount' => $existing!==null], null);
    }

    public function acceptInvitationPost(array $params): void
    {
        $token = (string) ($params['token'] ?? '');
        $invite = Database::first('SELECT * FROM partner_invitations WHERE token_hash=? AND accepted_at IS NULL AND expires_at>NOW()', [hash('sha256', $token)]);
        $password = Request::string('password');
        if (!$invite) {flash('error','Invitation is expired or already used.');redirect('/partner/login');}
        $existing=Database::first('SELECT id,password_hash,status FROM users WHERE email=?',[$invite['email']]);
        if($existing){
            if($existing['status']!=='active'||!password_verify($password,$existing['password_hash'])){flash('error','Enter the current password for this TCM One account to link partner access.');redirect('/partner/invitation/'.rawurlencode($token));}
        } elseif(strlen($password)<10||$password!==Request::string('password_confirm')) {flash('error','Choose a password of at least 10 characters and confirm it.');redirect('/partner/invitation/'.rawurlencode($token));}
        try { Database::transaction(function () use ($invite, $password, $existing): void {
            $current=Database::first('SELECT id,password_hash,status FROM users WHERE email=?',[$invite['email']]);
            if($current){
                if($current['status']!=='active'||!password_verify($password,$current['password_hash']))throw new \RuntimeException('The existing TCM One account could not be verified. Please sign in and request a new invitation.');
                $uid=(int)$current['id'];
            } else {
                if($existing!==null)throw new \RuntimeException('Account state changed. Request a new invitation.');
                $uid=Database::insert('users',['name'=>$invite['name'],'email'=>$invite['email'],'password_hash'=>password_hash($password,PASSWORD_DEFAULT),'role'=>$invite['role'],'status'=>'active']);
            }
            Database::run('INSERT INTO partner_memberships (partner_id,user_id,member_role,title,status,invited_at,accepted_at) VALUES (?,?,?,?,\'active\',?,NOW()) ON DUPLICATE KEY UPDATE member_role=VALUES(member_role),status=\'active\',accepted_at=NOW()', [$invite['partner_id'], $uid, $invite['role'], 'Coordinator', $invite['created_at']]);
            Database::update('partner_invitations', ['accepted_at' => date('Y-m-d H:i:s')], ['id' => $invite['id']]);
            Auth::login($uid);
        }); } catch (\RuntimeException $e) { flash('error',$e->getMessage()); redirect('/partner/login'); }
        redirect('/partner/dashboard');
    }

    private function financeTotals(int $partnerId): array
    {
        $r = Database::first("SELECT COALESCE(SUM(eligible_amount),0) collections,COALESCE(SUM(partner_entitlement),0) partner_earnings,COALESCE(SUM(CASE WHEN collection_owner='tcm' THEN partner_entitlement ELSE 0 END),0) payable,COALESCE(SUM(CASE WHEN collection_owner='partner' THEN tcm_entitlement ELSE 0 END),0) receivable FROM partner_ledger_entries WHERE partner_id=? AND status='verified'", [$partnerId]) ?? [];
        $s = Database::first("SELECT COALESCE(SUM(CASE WHEN direction='tcm_owes_partner' THEN amount ELSE 0 END),0) paid_to_partner,COALESCE(SUM(CASE WHEN direction='partner_owes_tcm' THEN amount ELSE 0 END),0) paid_to_tcm,COALESCE(SUM(amount),0) settled FROM partner_settlements WHERE partner_id=? AND status='settled'", [$partnerId]) ?? [];
        return ['collections' => (float) ($r['collections'] ?? 0), 'partner_earnings' => (float)($r['partner_earnings']??0), 'partner_entitlement' => (float) ($r['payable'] ?? 0), 'tcm_entitlement' => (float) ($r['receivable'] ?? 0), 'owes_partner' => max(0,(float)($r['payable']??0)-(float)($s['paid_to_partner']??0)), 'owes_tcm' => max(0,(float)($r['receivable']??0)-(float)($s['paid_to_tcm']??0)), 'settled' => (float) ($s['settled'] ?? 0)];
    }

    private function activity(int $partnerId, int $userId, string $action, array $details): void
    {
        Database::insert('partner_activity_logs', ['partner_id' => $partnerId, 'user_id' => $userId, 'action' => $action, 'details' => json_encode($details)]);
    }
}
