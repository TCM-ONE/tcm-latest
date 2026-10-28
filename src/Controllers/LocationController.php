<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Services\DiwaliOffer;

/** Search-friendly, truthful location pages for TCM One's online programmes. */
final class LocationController extends Controller
{
    /** @return array<string,array<string,string>> */
    private function locations(): array
    {
        return [
            'bengaluru' => [
                'name' => 'Bengaluru', 'region' => 'Karnataka', 'focus' => 'software engineering, product development and data careers',
                'intro' => 'Build practical software skills from Bengaluru with instructor-led online learning, guided projects and structured practice. TCM One cohorts are accessible remotely, so you can learn around college, work or interview preparation without a daily commute.',
                'audience' => 'Students, early-career developers and career switchers preparing for software roles in Bengaluru.',
                'localNote' => 'If you are balancing classes, a commute or interview preparation, start by comparing the weekly schedule and prerequisites on each course page. Web and Full Stack programmes can help you practise building complete applications; Python, data and AI options suit learners who want a different technical starting point. Choose based on the work you want to be able to demonstrate, not a course title alone.',
            ],
            'mumbai' => [
                'name' => 'Mumbai', 'region' => 'Maharashtra', 'focus' => 'technology, fintech, analytics and digital product careers',
                'intro' => 'Learn job-relevant coding and analytics skills from Mumbai through live online classes, guided projects and mentor support. TCM One programmes are delivered remotely, making cohort learning accessible across the city and surrounding areas.',
                'audience' => 'College students, working professionals and career switchers building practical tech skills in Mumbai.',
                'localNote' => 'For busy college or work schedules, compare cohort timings and the amount of weekly project practice before you enrol. Learners exploring software can start with web development or Full Stack; those leaning toward business and technology can compare analytics and AI programmes. Review the published fee, duration and delivery information to decide whether a cohort fits your plans.',
            ],
            'delhi-ncr' => [
                'name' => 'Delhi NCR', 'region' => 'Delhi, Haryana and Uttar Pradesh', 'focus' => 'software, startups, data and digital business careers',
                'intro' => 'Develop practical technology skills from Delhi NCR with live online cohorts, project-based learning and guided practice. Learn remotely from Delhi, Gurugram, Noida and nearby areas while keeping your existing study or work schedule.',
                'audience' => 'Students and aspiring developers across Delhi, Gurugram, Noida, Ghaziabad and Faridabad.',
                'localNote' => 'The online format lets learners join from different parts of NCR without travelling to a shared classroom. If you are preparing for internships or a first role, look for a course with a clear project outcome and a schedule you can maintain. Compare programming foundations, Full Stack, data and career-focused courses by prerequisites and curriculum.',
            ],
            'hyderabad' => [
                'name' => 'Hyderabad', 'region' => 'Telangana', 'focus' => 'software development, cloud, AI and data careers',
                'intro' => 'Build a stronger technology foundation from Hyderabad through live online classes and hands-on learning. TCM One combines instructor-led sessions with guided projects and structured practice for learners preparing for their next step in tech.',
                'audience' => 'Students, graduates and working professionals in Hyderabad who want structured, practical tech learning.',
                'localNote' => 'A structured course can help you turn independent study into a sequence of concepts, practice and project work. Compare web development, Python, cloud, AI and analytics paths against the skills you already have. Check the course outline and session timings first, then use the contact page if you need help identifying the right starting level.',
            ],
            'pune' => [
                'name' => 'Pune', 'region' => 'Maharashtra', 'focus' => 'software, engineering, cloud and data careers',
                'intro' => 'Study software development and modern technology from Pune with live online cohorts, practical projects and mentor guidance. Remote delivery lets learners across Pune join structured classes without needing an in-person campus nearby.',
                'audience' => 'Engineering students, graduates and professionals in Pune planning a technology career.',
                'localNote' => 'Whether you are strengthening coursework or making a career transition, use the syllabus to check how much coding practice and project work a course includes. Full Stack and frontend paths focus on building for the web; programming, data and cloud courses offer other ways to deepen your technical foundation. Each course page explains its own schedule and entry requirements.',
            ],
            'chennai' => [
                'name' => 'Chennai', 'region' => 'Tamil Nadu', 'focus' => 'software engineering, web development and analytics careers',
                'intro' => 'Learn coding and data skills from Chennai in a structured online cohort. TCM One programmes pair live instruction with guided projects and practice so you can build demonstrable skills alongside college or work.',
                'audience' => 'Students, early-career professionals and career changers in Chennai.',
                'localNote' => 'Start with the fundamentals you need next: programming practice, web application development, analytics or AI. The course pages outline the topics and delivery format, which makes it easier to match a programme to your current experience. Set aside regular time for the guided projects as well as attending the live sessions.',
            ],
            'kolkata' => [
                'name' => 'Kolkata', 'region' => 'West Bengal', 'focus' => 'web development, programming and data careers',
                'intro' => 'Build practical technology skills from Kolkata through live online learning, guided projects and instructor support. Learn remotely with a clear course structure and access programmes in web development, Python, AI and analytics.',
                'audience' => 'College learners, graduates and aspiring developers across Kolkata and West Bengal.',
                'localNote' => 'If you are exploring a technology career, compare beginner-level programming courses with focused options in web development, data or AI. Look for a clear curriculum and realistic time commitment, then use project work to practise explaining how you approached a problem. Remote cohorts let you join from Kolkata or elsewhere in West Bengal.',
            ],
            'ahmedabad' => [
                'name' => 'Ahmedabad', 'region' => 'Gujarat', 'focus' => 'software development, data analytics, AI and digital product careers',
                'intro' => 'Learners in Ahmedabad can join TCM One’s live online coding and technology programmes, with guided projects and mentor-led practice. Compare the current course outline, fee and cohort schedule before choosing a learning path.',
                'audience' => 'Students, graduates and working professionals in Ahmedabad building practical technology skills.',
                'localNote' => 'If you are preparing for a software or analytics role, begin with the skills the role expects and compare them with each course’s prerequisites. Full Stack programmes focus on building web applications; Python, data analytics and AI courses offer other practical paths. Cohorts are online, so confirm the current session timings and project expectations before enrolling.',
            ],
            'bilaspur' => [
                'name' => 'Bilaspur', 'region' => 'Chhattisgarh', 'focus' => 'coding, software development and career-oriented tech skills',
                'intro' => 'Learners in Bilaspur can join TCM One’s live online coding and technology programmes from home or college. The organisation is based in Waraseoni, Madhya Pradesh, and lists a Bilaspur campus as upcoming; current online programmes are available remotely.',
                'audience' => 'Students and aspiring developers in Bilaspur who want live instruction and hands-on tech learning.',
                'localNote' => 'You can join a live online cohort from Bilaspur without waiting for an in-person campus. If you are starting out, compare programming foundations and beginner-friendly web courses; if you already code, review focused Full Stack, Python, data or AI options. Confirm the current cohort schedule and delivery details with admissions before planning around any campus-based sessions.',
            ],
            'nagpur' => [
                'name' => 'Nagpur', 'region' => 'Maharashtra', 'focus' => 'software development, engineering, data analytics and AI careers',
                'intro' => 'Learners in Nagpur can build practical technology skills through TCM One’s live online classes, guided projects and structured practice. Join remotely from home or college and compare current course schedules before choosing a cohort.',
                'audience' => 'Students, graduates and working professionals in Nagpur preparing for software, data or AI roles.',
                'localNote' => 'For a first software role or internship, compare programming foundations, web development and Full Stack courses by their prerequisites and project work. If you are leaning toward analytics or AI, review the course outline and required tools. Online cohorts let you learn from Nagpur; confirm the current timings, fees and delivery format with admissions.',
            ],
            'balaghat' => [
                'name' => 'Balaghat', 'region' => 'Madhya Pradesh', 'focus' => 'coding foundations, web development and practical technology skills',
                'intro' => 'Students and aspiring developers in Balaghat can access TCM One’s instructor-led online technology programmes without travelling to another city. Classes combine live sessions with guided practice and project work.',
                'audience' => 'College learners, graduates and career changers in Balaghat looking for structured online tech learning.',
                'localNote' => 'Start with the skills you want to practise next. Beginner learners can compare programming and web foundations; learners with coding experience can explore Full Stack, data analytics or AI. Check the prerequisites, weekly schedule and current course details before enrolling in an online cohort from Balaghat.',
            ],
            'raipur' => [
                'name' => 'Raipur', 'region' => 'Chhattisgarh', 'focus' => 'software engineering, web development, data and AI skills',
                'intro' => 'Build technology skills from Raipur with TCM One’s live online classes, instructor guidance and hands-on projects. Remote cohorts give learners in Raipur access to structured coding and analytics programmes alongside college or work.',
                'audience' => 'Students, graduates and professionals in Raipur exploring software, web development, analytics or AI.',
                'localNote' => 'Compare course outcomes as well as titles: web and Full Stack programmes focus on building applications, while analytics and AI courses develop different technical skills. Review each programme’s schedule, prerequisites, fees and project expectations, then contact admissions if you need help selecting an online cohort from Raipur.',
            ],
            'durg' => [
                'name' => 'Durg', 'region' => 'Chhattisgarh', 'focus' => 'coding, software development, data analytics and career preparation',
                'intro' => 'Learners in Durg and the Durg–Bhilai area can join TCM One’s live online coding and technology classes. Guided projects and structured practice help you build skills remotely around your existing study or work schedule.',
                'audience' => 'Students and early-career professionals across Durg and Bhilai preparing for technology opportunities.',
                'localNote' => 'If you are beginning a tech career, compare programming fundamentals and web development with focused options in Full Stack, data analytics and AI. Check course prerequisites and live session timings first. TCM One’s online cohorts are accessible from Durg and Bhilai; ask admissions to confirm the current schedule and delivery format.',
            ],
        ];
    }

    public function index(): void
    {
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $this->view('public/locations/index', [
            'locations' => $this->locations(),
            'appUrl' => $appUrl,
            'canonical' => $appUrl . '/locations',
            'title' => 'Online Coding Courses in India | TCM One',
            'description' => 'Join live online coding, Full Stack, Python, AI and data analytics courses from Bengaluru, Mumbai, Delhi NCR and cities across India. Compare programmes and schedules.',
        ], null);
    }

    public function show(array $params): void
    {
        $slug = strtolower((string) ($params['slug'] ?? ''));
        $locations = $this->locations();
        if (!isset($locations[$slug])) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Location Not Found — TCM One',
                'message' => 'This location page is unavailable. Browse our online learning locations instead.',
            ], null);
            return;
        }

        $location = $locations[$slug];
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $title = 'Online Coding Classes in ' . $location['name'] . ' | TCM One';
        $description = 'Explore live online coding classes in ' . $location['name'] . ' with TCM One. Compare Full Stack, Python, AI and data analytics courses, project work, fees and cohort schedules.';
        $this->view('public/locations/show', [
            'location' => $location,
            'slug' => $slug,
            'locations' => $locations,
            'appUrl' => $appUrl,
            'canonical' => $appUrl . '/locations/' . $slug,
            'title' => $title,
            'description' => $description,
            'ogImage' => $appUrl . '/assets/tcm-og-share.png',
            'diwaliOfferActive' => DiwaliOffer::isActive(),
        ], null);
    }
}
