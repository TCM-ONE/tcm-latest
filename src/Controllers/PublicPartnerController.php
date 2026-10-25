<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Models\Lead;

final class PublicPartnerController extends Controller
{
    public function directory(): void
    {
        $city = Request::string('city'); $search = Request::string('q'); $type=Request::string('type'); $courseFilter=Request::string('course');
        $sql = "SELECT p.*,(SELECT COUNT(*) FROM partner_courses c WHERE c.partner_id=p.id AND c.review_status='approved' AND c.admissions_status IN ('open','upcoming')) course_count FROM partners p WHERE p.status='active' AND p.public_status='published'";
        $args=[];
        if ($city !== '') { $sql .= ' AND p.city = ?'; $args[]=$city; }
        if ($search !== '') { $sql .= ' AND (p.display_name LIKE ? OR p.city LIKE ? OR p.description LIKE ?)'; $like='%'.$search.'%'; array_push($args,$like,$like,$like); }
        if (in_array($type,['academic','referral','custom'],true)) { $sql .= ' AND p.partnership_type = ?'; $args[]=$type; }
        if ($courseFilter !== '') { $sql .= " AND EXISTS (SELECT 1 FROM partner_courses pc WHERE pc.partner_id=p.id AND pc.review_status='approved' AND pc.admissions_status IN ('open','upcoming') AND (pc.title LIKE ? OR pc.category LIKE ?))"; $like='%'.$courseFilter.'%'; $args[]=$like; $args[]=$like; }
        $sql .= ' ORDER BY p.display_name';
        $partners=Database::all($sql,$args);
        $cities=Database::all("SELECT DISTINCT city FROM partners WHERE status='active' AND public_status='published' AND city IS NOT NULL ORDER BY city");
        $this->view('public/partners/index',['title'=>'Our Institutional Partners','partners'=>$partners,'cities'=>$cities,'city'=>$city,'search'=>$search,'type'=>$type,'courseFilter'=>$courseFilter],'public');
    }

    public function show(array $params): void
    {
        $partner=Database::first("SELECT * FROM partners WHERE slug=? AND status='active' AND public_status='published'",[(string)$params['slug']]);
        if (!$partner) { http_response_code(404); $this->view('errors/404',[], 'public'); return; }
        $courses=Database::all("SELECT * FROM partner_courses WHERE partner_id=? AND review_status='approved' AND admissions_status IN ('open','upcoming') ORDER BY title",[$partner['id']]);
        $this->view('public/partners/show',['title'=>$partner['display_name'],'partner'=>$partner,'courses'=>$courses],'public');
    }

    public function enquiry(array $params): void
    {
        $partner=Database::first("SELECT id,display_name FROM partners WHERE slug=? AND status='active' AND public_status='published'",[(string)$params['slug']]);
        if (!$partner) { http_response_code(404); exit('Partner not found.'); }
        $name=Request::string('name'); $email=strtolower(Request::string('email')); $phone=Request::string('phone');
        $courseId=Request::int('course_id') ?: null;
        if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || Request::int('consent')!==1) {flash('error','Name, valid email, and consent are required to submit an enquiry.');redirect('/partners/'.rawurlencode((string)$params['slug']));}
        if ($courseId && !Database::first("SELECT id FROM partner_courses WHERE id=? AND partner_id=? AND review_status='approved'",[$courseId,$partner['id']])) {http_response_code(400);exit('Invalid course selection.');}
        $duplicate=Database::first('SELECT id FROM partner_public_enquiries WHERE partner_id=? AND partner_course_id <=> ? AND LOWER(email)=LOWER(?) AND created_at>DATE_SUB(NOW(),INTERVAL 24 HOUR) LIMIT 1',[$partner['id'],$courseId,$email]);
        if (!$duplicate) {
            Database::transaction(function()use($partner,$courseId,$name,$email,$phone):void {
                Database::insert('partner_public_enquiries',['partner_id'=>$partner['id'],'partner_course_id'=>$courseId,'name'=>$name,'email'=>$email,'phone'=>$phone?:null,'message'=>Request::string('message')?:null,'consent_at'=>date('Y-m-d H:i:s')]);
                try { Lead::capture(['name'=>$name,'email'=>$email,'phone'=>$phone,'interest_type'=>'general','interest_title'=>'Partner enquiry: '.$partner['display_name'],'message'=>Request::string('message'),'source'=>'partner_directory']); } catch (\Throwable $e) { error_log('Partner enquiry lead sync failed: '.$e->getMessage()); }
            });
        }
        flash('success',$duplicate?'Your recent enquiry is already with the institution.':'Enquiry sent. The institution can now follow up with you.');
        redirect('/partners/'.rawurlencode((string)$params['slug']));
    }
}
