<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\WhatsApp;
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
        $directoryDescription = 'Explore TCM One’s institutional partners, local education centres and current partner courses, including JMK Computer Education Centre in Bilaspur, Chhattisgarh.';
        $this->view('public/partners/index',['title'=>'Education Partners in India | TCM One','description'=>$directoryDescription,'canonicalUrl'=>base_url('/partners'),'socialTitle'=>'TCM One Education Partners','socialDescription'=>$directoryDescription,'socialImage'=>base_url('/assets/tcm-og-share.png'),'partners'=>$partners,'cities'=>$cities,'city'=>$city,'search'=>$search,'type'=>$type,'courseFilter'=>$courseFilter],'public');
    }

    public function show(array $params): void
    {
        $partner=Database::first("SELECT * FROM partners WHERE slug=? AND status='active' AND public_status='published'",[(string)$params['slug']]);
        if (!$partner) { http_response_code(404); $this->view('errors/404',[], 'public'); return; }
        $courses=Database::all("SELECT * FROM partner_courses WHERE partner_id=? AND review_status='approved' AND admissions_status IN ('open','upcoming') ORDER BY title",[$partner['id']]);
        $isJmk = $partner['slug'] === 'jmk-computer-education-center';
        $mapEmbedUrl = 'https://www.google.com/maps?q=' . rawurlencode((string)($partner['address'] ?? (($partner['city'] ?? '') . ', ' . ($partner['state'] ?? '')))) . '&output=embed';
        $googleReviewsUrl = $isJmk
            ? 'https://www.google.com/maps/place/jmk+computer+education+center/data=!4m2!3m1!1s0x0:0x6a1b0bfa7e567bdd'
            : 'https://www.google.com/search?q=' . rawurlencode($partner['display_name'] . ' ' . ($partner['city'] ?? '') . ' reviews');
        $canonicalUrl = base_url('/partners/' . rawurlencode((string)$partner['slug']));
        $pageDescription = $isJmk ? 'Explore JMK Education Centre in Ashok Nagar, Sarkanda, Bilaspur: Classes 1–12 tuition, ADCA, PGDCA, Tally, programming, typing and other listed courses, with map and contact details.' : mb_strimwidth((string)($partner['description'] ?? ''),0,155,'…');
        $this->view('public/partners/show',[
            'title'=>$isJmk ? 'JMK Computer Education Centre in Bilaspur' : $partner['display_name'],
            'description'=>$pageDescription,
            'canonicalUrl'=>$canonicalUrl,
            'socialTitle'=>$isJmk ? 'JMK Computer Education Centre · Bilaspur' : $partner['display_name'],
            'socialDescription'=>$pageDescription,
            'socialImage'=>!empty($partner['logo_path']) ? base_url('/'.ltrim((string)$partner['logo_path'],'/')) : base_url('/assets/tcm-og-share.png'),
            'partner'=>$partner,
            'courses'=>$courses,
            'mapEmbedUrl'=>$mapEmbedUrl,
            'googleReviewsUrl'=>$googleReviewsUrl,
            'shareUrl'=>$canonicalUrl,
            'isJmk'=>$isJmk,
        ],'public');
    }

    public function enquiry(array $params): void
    {
        $partner=Database::first("SELECT id,display_name FROM partners WHERE slug=? AND status='active' AND public_status='published'",[(string)$params['slug']]);
        if (!$partner) { http_response_code(404); exit('Partner not found.'); }
        $name=Request::string('name'); $email=strtolower(Request::string('email')); $phone=Request::string('phone'); $message=Request::string('message');
        $courseId=Request::int('course_id') ?: null;
        if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || Request::int('consent')!==1) {flash('error','Name, valid email, and consent are required to submit an enquiry.');redirect('/partners/'.rawurlencode((string)$params['slug']));}
        $selectedCourse = $courseId ? Database::first("SELECT id,title FROM partner_courses WHERE id=? AND partner_id=? AND review_status='approved'",[$courseId,$partner['id']]) : null;
        if ($courseId && !$selectedCourse) {http_response_code(400);exit('Invalid course selection.');}
        $duplicate=Database::first('SELECT id FROM partner_public_enquiries WHERE partner_id=? AND partner_course_id <=> ? AND LOWER(email)=LOWER(?) AND created_at>DATE_SUB(NOW(),INTERVAL 24 HOUR) LIMIT 1',[$partner['id'],$courseId,$email]);
        if (!$duplicate) {
            Database::transaction(function()use($partner,$courseId,$name,$email,$phone,$message):void {
                Database::insert('partner_public_enquiries',['partner_id'=>$partner['id'],'partner_course_id'=>$courseId,'name'=>$name,'email'=>$email,'phone'=>$phone?:null,'message'=>$message?:null,'consent_at'=>date('Y-m-d H:i:s')]);
                try { Lead::capture(['name'=>$name,'email'=>$email,'phone'=>$phone,'interest_type'=>'general','interest_title'=>'Partner enquiry: '.$partner['display_name'],'message'=>$message,'source'=>'partner_directory']); } catch (\Throwable $e) { error_log('Partner enquiry lead sync failed: '.$e->getMessage()); }
            });
        }
        $courseTitle = $selectedCourse['title'] ?? 'General enquiry';
        $whatsappMessage = implode("\n", [
            'New partner enquiry · ' . $partner['display_name'],
            'Name: ' . $name,
            'Email: ' . $email,
            'Phone: ' . ($phone !== '' ? $phone : 'Not provided'),
            'Course: ' . $courseTitle,
            'Message: ' . ($message !== '' ? $message : 'No message provided'),
            'Profile: ' . base_url('/partners/' . rawurlencode((string)$params['slug'])),
            $duplicate ? 'Note: recent matching enquiry already exists in the site inbox.' : '',
        ]);
        redirect(WhatsApp::link($whatsappMessage, '919238695500'));
    }
}
