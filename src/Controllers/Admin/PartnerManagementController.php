<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Mailer;
use TCM\Core\Request;
use TCM\Services\PartnerFinance;

final class PartnerManagementController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');
        $partners = Database::all('SELECT p.*, (SELECT COUNT(*) FROM partner_memberships pm WHERE pm.partner_id=p.id AND pm.status=\'active\') users_count,
            (SELECT COUNT(*) FROM partner_student_attributions a WHERE a.partner_id=p.id AND a.status IN (\'enrolled\',\'active\',\'completed\')) student_count,
            (SELECT COUNT(*) FROM partner_batches b WHERE b.partner_id=p.id AND b.status IN (\'admissions_open\',\'upcoming\',\'active\')) batch_count
            FROM partners p ORDER BY p.id DESC');
        $stats = [
            'total' => count($partners),
            'active' => count(array_filter($partners, static fn ($p) => $p['status'] === 'active')),
            'pending' => count(array_filter($partners, static fn ($p) => in_array($p['status'], ['new_lead','agreement_pending','onboarding'], true))),
            'students' => (int) Database::scalar("SELECT COUNT(*) FROM partner_student_attributions WHERE status IN ('enrolled','active','completed')"),
            'requests' => (int) Database::scalar("SELECT COUNT(*) FROM partner_service_requests WHERE status IN ('submitted','under_review','open','in_progress')"),
        ];
        $this->view('admin/partners/index', ['title' => 'Institutional Partners', 'partners' => $partners, 'stats' => $stats], 'admin');
    }

    public function store(): void
    {
        $admin = Auth::require('admin');
        $name = Request::string('display_name');
        $legal = Request::string('legal_name');
        $email = strtolower(Request::string('email'));
        $initialPassword=Request::string('initial_password');
        if (mb_strlen($name) < 2 || mb_strlen($legal) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Institution name, legal name, and a valid owner email are required.'); redirect('/admin/partners/create');
        }
        if($initialPassword!==''&&(strlen($initialPassword)<10||$initialPassword!==Request::string('initial_password_confirm'))){flash('error','Use a password of at least 10 characters and confirm it, or leave both password fields blank to send a setup invitation.');redirect('/admin/partners/create');}
        $existingUser=Database::first('SELECT id,status FROM users WHERE email=?',[$email]);
        if ($existingUser && Database::first('SELECT id FROM partner_memberships WHERE user_id=?',[$existingUser['id']])) { flash('error','This account is already linked to a partner. Use a different owner account.'); redirect('/admin/partners/create'); }
        if($existingUser&&$existingUser['status']!=='active'){flash('error','This TCM One account is suspended or pending. Activate it before granting partner access.');redirect('/admin/partners/create');}
        if (Database::first('SELECT id FROM partners WHERE LOWER(legal_name)=LOWER(?) OR email=?', [$legal,$email])) { flash('error','An institution with this legal name or email already exists.'); redirect('/admin/partners/create'); }
        $slugBase = trim((string) preg_replace('/[^a-z0-9]+/i','-',strtolower($name)),'-'); $slug=$slugBase; $n=2;
        while (Database::first('SELECT id FROM partners WHERE slug=?',[$slug])) $slug=$slugBase.'-'.$n++;
        $code = 'TCM-P-' . str_pad((string) (((int) Database::scalar('SELECT COALESCE(MAX(id),0) FROM partners')) + 1), 4, '0', STR_PAD_LEFT);
        $model = in_array(Request::string('partnership_type'), ['academic','referral','custom'], true) ? Request::string('partnership_type') : 'academic';
        $created = Database::transaction(function () use ($admin,$name,$legal,$email,$slug,$code,$model,$existingUser,$initialPassword): array {
            $id = Database::insert('partners', ['partner_code'=>$code,'legal_name'=>$legal,'display_name'=>$name,'slug'=>$slug,'contact_person'=>Request::string('contact_person') ?: null,'email'=>$email,'phone'=>Request::string('phone') ?: null,'institution_type'=>Request::string('institution_type') ?: null,'partnership_type'=>$model,'status'=>'agreement_pending','city'=>Request::string('city') ?: null,'state'=>Request::string('state') ?: null,'created_by'=>$admin['id']]);
            $version = 1;
            $isAcademic = $model === 'academic';
            $isReferral = $model === 'referral';
            Database::insert('partner_agreements', ['partner_id'=>$id,'version_no'=>$version,'model'=>$model,'effective_from'=>Request::string('effective_from') ?: date('Y-m-d'),'effective_to'=>Request::string('effective_to') ?: null,'institute_share'=>$isAcademic ? 40 : (is_numeric(Request::string('institute_share')) ? Request::string('institute_share') : null),'tcm_share'=>$isAcademic ? 60 : (is_numeric(Request::string('tcm_share')) ? Request::string('tcm_share') : null),'referral_commission'=>$isReferral ? 15 : (is_numeric(Request::string('referral_commission')) ? Request::string('referral_commission') : null),'payout_frequency'=>'monthly','status'=>'pending']);
            if($existingUser){
                Database::insert('partner_memberships',['partner_id'=>$id,'user_id'=>$existingUser['id'],'member_role'=>'partner_owner','title'=>'Owner','status'=>'active','accepted_at'=>date('Y-m-d H:i:s')]);
                return ['id'=>$id,'access'=>'linked','sent'=>true];
            }
            if($initialPassword!==''){
                $uid=Database::insert('users',['name'=>Request::string('contact_person')?:$name,'email'=>$email,'password_hash'=>password_hash($initialPassword,PASSWORD_DEFAULT),'role'=>'partner_owner','phone'=>Request::string('phone')?:null,'status'=>'active']);
                Database::insert('partner_memberships',['partner_id'=>$id,'user_id'=>$uid,'member_role'=>'partner_owner','title'=>'Owner','status'=>'active','accepted_at'=>date('Y-m-d H:i:s')]);
                return ['id'=>$id,'access'=>'created','sent'=>true];
            }
            $rawToken = bin2hex(random_bytes(32));
            Database::insert('partner_invitations',['partner_id'=>$id,'email'=>$email,'name'=>Request::string('contact_person') ?: $name,'role'=>'partner_owner','token_hash'=>hash('sha256',$rawToken),'expires_at'=>date('Y-m-d H:i:s',strtotime('+72 hours')),'created_by'=>$admin['id']]);
            $url = base_url('/partner/invitation/'.$rawToken);
            $sent=Mailer::send($email,'TCM One Partner Portal invitation','<p>Hello '.e(Request::string('contact_person') ?: $name).',</p><p>Set up access for <strong>'.e($name).'</strong>: <a href="'.e($url).'">Accept invitation</a>. The link expires in 72 hours.</p>');
            return ['id'=>$id,'access'=>'invited','sent'=>$sent];
        });
        $accessMessage=match($created['access']){'linked'=>'Existing TCM One account linked; the owner keeps the same password.','created'=>'Partner login created. Share the login email and initial password with the owner securely.','invited'=>$created['sent']?'Owner setup invitation sent.':'Invitation email failed; resend it after checking SMTP or create access with a password.'};
        flash($created['access']==='invited'&&!$created['sent']?'info':'success', 'Partner ' . $code . ' created. Agreement stays pending until activation. ' . $accessMessage);
        redirect('/admin/partners/' . $created['id']);
    }

    public function create(): void
    {
        Auth::require('admin');
        $this->view('admin/partners/create', ['title'=>'Add Partner'], 'admin');
    }

    public function show(array $params): void
    {
        Auth::require('admin');
        $partner = Database::first('SELECT * FROM partners WHERE id=?',[(int)$params['id']]);
        if (!$partner) { http_response_code(404); echo 'Partner not found.'; return; }
        $members=Database::all('SELECT u.name,u.email,pm.title,pm.member_role,pm.status,pm.can_view_finance FROM partner_memberships pm JOIN users u ON u.id=pm.user_id WHERE pm.partner_id=?',[$partner['id']]);
        $requests=Database::all('SELECT * FROM partner_service_requests WHERE partner_id=? ORDER BY id DESC LIMIT 30',[$partner['id']]);
        $ledger=Database::all('SELECT * FROM partner_ledger_entries WHERE partner_id=? ORDER BY id DESC LIMIT 30',[$partner['id']]);
        $batches=Database::all('SELECT b.*,c.title AS course_title FROM partner_batches b LEFT JOIN courses c ON c.id=b.course_id WHERE b.partner_id=? ORDER BY b.id DESC',[$partner['id']]);
        $this->view('admin/partners/show',['title'=>$partner['display_name'],'partner'=>$partner,'members'=>$members,'requests'=>$requests,'ledger'=>$ledger,'batches'=>$batches],'admin');
    }

    public function updateStatus(array $params): void
    {
        $admin=Auth::require('admin'); $id=(int)$params['id'];
        $status=Request::string('status');
        if (!in_array($status,['new_lead','agreement_pending','onboarding','active','paused','expired','terminated'],true)) {flash('error','Invalid partner status.');redirect('/admin/partners/'.$id);}
        Database::update('partners',['status'=>$status],['id'=>$id]);
        if ($status==='active') Database::run("UPDATE partner_agreements SET status='active',approved_by=?,approved_at=NOW() WHERE partner_id=? AND status='pending'",[$admin['id'],$id]);
        flash('success','Partner status updated.');redirect('/admin/partners/'.$id);
    }

    public function requests(): void
    {
        Auth::require('admin');
        $rows=Database::all('SELECT r.*,p.display_name,u.name AS submitted_by FROM partner_service_requests r JOIN partners p ON p.id=r.partner_id JOIN users u ON u.id=r.user_id ORDER BY r.id DESC LIMIT 250');
        $this->view('admin/partners/requests',['title'=>'Partner Requests','rows'=>$rows],'admin');
    }

    public function updateRequest(array $params): void
    {
        $admin=Auth::require('admin'); $id=(int)$params['id'];
        $status=Request::string('status');
        $allowed=['under_review','additional_information','shortlisted','demo_scheduled','mentor_assigned','approved','rejected','scheduled','completed','open','in_progress','waiting_partner','resolved','closed','cancelled'];
        if (!in_array($status,$allowed,true)) {flash('error','Invalid request status.');redirect('/admin/partners/requests');}
        Database::update('partner_service_requests',['status'=>$status,'admin_note'=>Request::string('admin_note') ?: null,'assigned_to'=>$admin['id']],['id'=>$id]);
        flash('success','Request updated.');redirect('/admin/partners/requests');
    }

    public function courses(): void
    {
        Auth::require('admin');
        $rows=Database::all('SELECT c.*,p.display_name FROM partner_courses c JOIN partners p ON p.id=c.partner_id ORDER BY c.id DESC');
        $this->view('admin/partners/courses',['title'=>'Partner Courses','rows'=>$rows],'admin');
    }

    public function reviewCourse(array $params): void
    {
        $admin=Auth::require('admin'); $id=(int)$params['id']; $status=Request::string('status');
        if (!in_array($status,['approved','rejected','unpublished'],true)) {flash('error','Invalid course review action.');redirect('/admin/partners/courses');}
        Database::update('partner_courses',['review_status'=>$status,'rejection_reason'=>$status==='rejected' ? Request::string('reason') : null,'reviewed_by'=>$admin['id'],'reviewed_at'=>date('Y-m-d H:i:s')],['id'=>$id]);
        flash('success','Partner course review saved.');redirect('/admin/partners/courses');
    }

    public function reviewBatch(array $params): void
    {
        $admin=Auth::require('admin'); $id=(int)$params['id']; $status=Request::string('status');
        if (!in_array($status,['admissions_open','upcoming','active','cancelled'],true)) {flash('error','Invalid batch review action.');redirect('/admin/partners');}
        Database::update('partner_batches',['status'=>$status,'approved_by'=>$admin['id'],'approved_at'=>date('Y-m-d H:i:s')],['id'=>$id]);
        flash('success','Partner batch updated.');redirect('/admin/partners');
    }

    public function updatePublicStatus(array $params): void
    {
        Auth::require('admin'); $id=(int)$params['id']; $status=Request::string('public_status');
        if (!in_array($status,['draft','pending_review','published','rejected'],true)) {flash('error','Invalid directory status.');redirect('/admin/partners/'.$id);}
        $partner=Database::first('SELECT status FROM partners WHERE id=?',[$id]);
        if (!$partner) {http_response_code(404);exit('Partner not found.');}
        if ($status==='published' && $partner['status']!=='active') {flash('error','Activate the signed partner agreement before publishing its directory profile.');redirect('/admin/partners/'.$id);}
        Database::update('partners',['public_status'=>$status],['id'=>$id]);
        flash('success','Public directory status updated.');redirect('/admin/partners/'.$id);
    }

    public function resendInvitation(array $params): void
    {
        $admin=Auth::require('admin'); $id=(int)$params['id'];
        $partner=Database::first('SELECT id,display_name,email,contact_person FROM partners WHERE id=?',[$id]);
        if(!$partner||!filter_var($partner['email'],FILTER_VALIDATE_EMAIL)){flash('error','Partner invitation email is missing or invalid.');redirect('/admin/partners/'.$id);}
        $existing=Database::first('SELECT id FROM users WHERE email=?',[$partner['email']]);
        if($existing&&Database::first('SELECT id FROM partner_memberships WHERE user_id=?',[$existing['id']])){flash('error','This account is already linked to another partner.');redirect('/admin/partners/'.$id);}
        $token=bin2hex(random_bytes(32));
        Database::insert('partner_invitations',['partner_id'=>$id,'email'=>$partner['email'],'name'=>$partner['contact_person']?:$partner['display_name'],'role'=>'partner_owner','token_hash'=>hash('sha256',$token),'expires_at'=>date('Y-m-d H:i:s',strtotime('+72 hours')),'created_by'=>$admin['id']]);
        $sent=Mailer::send($partner['email'],'TCM One Partner Portal invitation','<p>Hello '.e($partner['contact_person']?:$partner['display_name']).',</p><p><a href="'.e(base_url('/partner/invitation/'.$token)).'">Accept the TCM One partner portal invitation</a>. The link expires in 72 hours.</p>');
        flash($sent?'success':'error',$sent?'Invitation resent.':'Email delivery failed; check SMTP settings and try again.');redirect('/admin/partners/'.$id);
    }

    public function finance(): void
    {
        Auth::require('admin');
        $paymentExists=(bool)Database::scalar("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name='payment_submissions'");
        $payments=$paymentExists?Database::all("SELECT ps.id,ps.user_id,ps.item_title,ps.amount,ps.payment_date,ps.transaction_ref,u.name AS student_name,p.id AS partner_id,p.display_name FROM payment_submissions ps JOIN users u ON u.id=ps.user_id JOIN partner_student_attributions a ON a.student_user_id=ps.user_id AND a.status IN ('enrolled','active','completed') JOIN partners p ON p.id=a.partner_id AND p.status='active' LEFT JOIN partner_ledger_entries l ON l.payment_submission_id=ps.id WHERE ps.status='approved' AND l.id IS NULL ORDER BY ps.payment_date DESC LIMIT 200"):[];
        $partners=Database::all("SELECT id,display_name FROM partners WHERE status='active' ORDER BY display_name");
        $entries=Database::all('SELECT l.*,p.display_name,u.name AS student_name FROM partner_ledger_entries l JOIN partners p ON p.id=l.partner_id LEFT JOIN users u ON u.id=l.student_user_id ORDER BY l.id DESC LIMIT 200');
        $settlements=Database::all('SELECT s.*,p.display_name FROM partner_settlements s JOIN partners p ON p.id=s.partner_id ORDER BY s.id DESC LIMIT 100');
        $this->view('admin/partners/finance',['title'=>'Partner Ledger & Settlements','payments'=>$payments,'partners'=>$partners,'entries'=>$entries,'settlements'=>$settlements],'admin');
    }

    public function students(): void
    {
        Auth::require('admin');
        $rows=Database::all('SELECT a.*,p.display_name,u.name,u.email,c.title AS course_title,b.batch_name FROM partner_student_attributions a JOIN partners p ON p.id=a.partner_id JOIN users u ON u.id=a.student_user_id LEFT JOIN courses c ON c.id=a.course_id LEFT JOIN partner_batches b ON b.id=a.batch_id ORDER BY a.id DESC LIMIT 400');
        $this->view('admin/partners/students',['title'=>'Partner Students','rows'=>$rows],'admin');
    }

    public function reviewStudent(array $params): void
    {
        $admin=Auth::require('admin'); $id=(int)$params['id']; $status=Request::string('status');
        if(!in_array($status,['lead','contacted','interested','admission_pending','enrolled','active','completed','dropped','rejected'],true)){flash('error','Invalid student status.');redirect('/admin/partners/students');}
        $verified=in_array($status,['enrolled','active','completed'],true);
        if($verified){
            $record=Database::first("SELECT a.partner_id,b.starts_on,COALESCE(ag.batch_lock_days,14) batch_lock_days FROM partner_student_attributions a LEFT JOIN partner_batches b ON b.id=a.batch_id LEFT JOIN partner_agreements ag ON ag.partner_id=a.partner_id AND ag.status='active' AND ag.effective_from<=CURDATE() AND (ag.effective_to IS NULL OR ag.effective_to>=CURDATE()) WHERE a.id=?",[$id]);
            if($record&&$record['starts_on']&&new \DateTimeImmutable('today')>=(new \DateTimeImmutable($record['starts_on']))->modify('+'.(int)$record['batch_lock_days'].' days')){flash('error','This batch has passed its enrollment lock date. Ask TCM One operations to review the exception.');redirect('/admin/partners/students');}
        }
        Database::update('partner_student_attributions',['status'=>$status,'verified_by'=>$verified?$admin['id']:null,'verified_at'=>$verified?date('Y-m-d H:i:s'):null],['id'=>$id]);
        flash('success','Partner student record reviewed.');redirect('/admin/partners/students');
    }

    public function recordVerifiedPayment(array $params): void
    {
        $admin=Auth::require('admin'); $paymentId=(int)$params['id'];
        $sub=Database::first("SELECT * FROM payment_submissions WHERE id=? AND status='approved'",[$paymentId]);
        if(!$sub){flash('error','Only approved payments can enter the partner ledger.');redirect('/admin/partners/finance');}
        $partnerId=Request::int('partner_id'); $partner=Database::first("SELECT * FROM partners WHERE id=? AND status='active'",[$partnerId]);
        $attribution=Database::first('SELECT a.*,u.created_at AS student_registered_at FROM partner_student_attributions a JOIN users u ON u.id=a.student_user_id WHERE a.partner_id=? AND a.student_user_id=? AND a.status IN (\'enrolled\',\'active\',\'completed\')',[$partnerId,$sub['user_id']]);
        if(!$partner||!$attribution){flash('error','Select the partner that owns this verified student attribution.');redirect('/admin/partners/finance');}
        $agreement=Database::first("SELECT * FROM partner_agreements WHERE partner_id=? AND status='active' AND effective_from<=? AND (effective_to IS NULL OR effective_to>=?) ORDER BY version_no DESC LIMIT 1",[$partnerId,$sub['payment_date'],$sub['payment_date']]);
        if(!$agreement){flash('error','No approved agreement applies on this payment date.');redirect('/admin/partners/finance');}
        if($agreement['model']==='referral'){
            $attributedAt=new \DateTimeImmutable($attribution['student_registered_at']);
            $paymentAt=new \DateTimeImmutable($sub['payment_date']);
            if($paymentAt<$attributedAt->setTime(0,0)||$paymentAt>$attributedAt->modify('+'.(int)$agreement['referral_window_days'].' days')){flash('error','This payment falls outside the agreement referral attribution window.');redirect('/admin/partners/finance');}
        }
        $gross=(float)$sub['amount']; $tax=(float)Request::string('tax_amount','0'); $gateway=(float)Request::string('gateway_fee','0');
        if($gross<=0){flash('error','Zero-value payments cannot create financial ledger entries.');redirect('/admin/partners/finance');}
        try { $allocation=PartnerFinance::allocate($gross,$tax,$gateway,0.0,$agreement['model'],isset($agreement['institute_share'])?(float)$agreement['institute_share']:null,isset($agreement['tcm_share'])?(float)$agreement['tcm_share']:null,isset($agreement['referral_commission'])?(float)$agreement['referral_commission']:null); }
        catch(\InvalidArgumentException $e) {flash('error',$e->getMessage());redirect('/admin/partners/finance');}
        $owner=in_array(Request::string('collection_owner'),['tcm','partner'],true)?Request::string('collection_owner'):'tcm';
        $batchId=Request::int('batch_id')?:null;
        if($batchId&&!Database::first('SELECT id FROM partner_batches WHERE id=? AND partner_id=?',[$batchId,$partnerId])){flash('error','Selected batch does not belong to that partner.');redirect('/admin/partners/finance');}
        $order=Database::first("SELECT id FROM orders WHERE user_id=? AND status='paid' AND item_title=? ORDER BY id DESC LIMIT 1",[$sub['user_id'],$sub['item_title']]);
        try {
            Database::insert('partner_ledger_entries',['partner_id'=>$partnerId,'agreement_id'=>$agreement['id'],'student_user_id'=>$sub['user_id'],'batch_id'=>$batchId,'order_id'=>$order['id']??null,'payment_submission_id'=>$paymentId,'entry_type'=>'collection','collection_owner'=>$owner,'gross_amount'=>$gross,'tax_amount'=>$tax,'gateway_fee'=>$gateway,'eligible_amount'=>$allocation['eligible'],'partner_entitlement'=>$allocation['partner'],'tcm_entitlement'=>$allocation['tcm'],'status'=>'verified','idempotency_key'=>'payment-submission-'.$paymentId,'description'=>$sub['item_title'].' · receipt '.($sub['receipt_number']??''),'created_by'=>$admin['id'],'verified_by'=>$admin['id'],'verified_at'=>date('Y-m-d H:i:s')]);
        } catch(\PDOException $e) { if(str_contains($e->getMessage(),'uniq_ledger_idempotency')){flash('info','This verified payment is already recorded.');redirect('/admin/partners/finance');} throw $e; }
        flash('success','Verified payment recorded in the bilateral ledger.');redirect('/admin/partners/finance');
    }

    public function createSettlement(): void
    {
        $admin=Auth::require('admin'); $partnerId=Request::int('partner_id'); $direction=Request::string('direction'); $amount=(float)Request::string('amount','0');
        if(!in_array($direction,['tcm_owes_partner','partner_owes_tcm'],true)||$amount<=0){flash('error','Choose a settlement direction and positive amount.');redirect('/admin/partners/finance');}
        $partner=Database::first("SELECT id FROM partners WHERE id=? AND status='active'",[$partnerId]);
        if(!$partner){flash('error','Active partner not found.');redirect('/admin/partners/finance');}
        try { Database::transaction(function()use($admin,$partnerId,$direction,$amount):void {
            Database::first('SELECT id FROM partners WHERE id=? FOR UPDATE',[$partnerId]);
            $ledger=Database::first("SELECT COALESCE(SUM(CASE WHEN collection_owner='tcm' THEN partner_entitlement ELSE 0 END),0) payable,COALESCE(SUM(CASE WHEN collection_owner='partner' THEN tcm_entitlement ELSE 0 END),0) receivable FROM partner_ledger_entries WHERE partner_id=? AND status='verified'",[$partnerId])??[];
            $committed=Database::first("SELECT COALESCE(SUM(amount),0) total FROM partner_settlements WHERE partner_id=? AND direction=? AND status NOT IN ('disputed','on_hold')",[$partnerId,$direction])??[];
            $entitlement=$direction==='tcm_owes_partner'?(float)($ledger['payable']??0):(float)($ledger['receivable']??0);
            $outstanding=max(0,$entitlement-(float)($committed['total']??0));
            if($amount>$outstanding)throw new \InvalidArgumentException('Settlement amount exceeds the verified outstanding balance after pending requests.');
            Database::insert('partner_settlements',['partner_id'=>$partnerId,'direction'=>$direction,'amount'=>round($amount,2),'status'=>'pending_verification','note'=>Request::string('note')?:null,'created_by'=>$admin['id']]);
        }); } catch(\InvalidArgumentException $e) {flash('error',$e->getMessage());redirect('/admin/partners/finance');}
        flash('success','Settlement request created for finance verification.');redirect('/admin/partners/finance');
    }

    public function updateSettlement(array $params): void
    {
        $admin=Auth::require('admin'); $id=(int)$params['id']; $action=Request::string('action');
        $settlement=Database::first('SELECT * FROM partner_settlements WHERE id=?',[$id]);
        if(!$settlement){flash('error','Settlement not found.');redirect('/admin/partners/finance');}
        if($action==='approve' && $settlement['status']==='pending_verification'){
            Database::update('partner_settlements',['status'=>'approved','approved_by'=>$admin['id'],'approved_at'=>date('Y-m-d H:i:s')],['id'=>$id]);
            flash('success','Settlement approved. It is not marked paid until the transfer is recorded.');
        } elseif($action==='settled' && $settlement['status']==='approved' && Request::string('reference')!==''){
            Database::update('partner_settlements',['status'=>'settled','reference'=>Request::string('reference'),'settled_at'=>date('Y-m-d H:i:s')],['id'=>$id]);
            flash('success','Actual settlement transfer recorded.');
        } else { flash('error','Invalid settlement transition. An approved transfer needs a reference to mark it settled.'); }
        redirect('/admin/partners/finance');
    }
}
