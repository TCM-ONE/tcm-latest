<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;

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
            'bilaspur' => [
                'name' => 'Bilaspur', 'region' => 'Chhattisgarh', 'focus' => 'coding, software development and career-oriented tech skills',
                'intro' => 'Learners in Bilaspur can join TCM One’s live online coding and technology programmes from home or college. The organisation is based in Waraseoni, Madhya Pradesh, and lists a Bilaspur campus as upcoming; current online programmes are available remotely.',
                'audience' => 'Students and aspiring developers in Bilaspur who want live instruction and hands-on tech learning.',
                'localNote' => 'You can join a live online cohort from Bilaspur without waiting for an in-person campus. If you are starting out, compare programming foundations and beginner-friendly web courses; if you already code, review focused Full Stack, Python, data or AI options. Confirm the current cohort schedule and delivery details with admissions before planning around any campus-based sessions.',
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
            'title' => 'Online Coding Classes Across India — TCM One',
            'description' => 'Explore live online coding, software development, Python, AI and data analytics programmes for learners in Bengaluru, Mumbai, Delhi NCR, Hyderabad, Pune, Chennai, Kolkata and Bilaspur.',
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
        $description = 'Explore live online coding classes in ' . $location['name'] . ': Full Stack, Python, AI, data analytics and guided projects with TCM One.';
        $this->view('public/locations/show', [
            'location' => $location,
            'slug' => $slug,
            'locations' => $locations,
            'appUrl' => $appUrl,
            'canonical' => $appUrl . '/locations/' . $slug,
            'title' => $title,
            'description' => $description,
            'ogImage' => $appUrl . '/assets/tcm-og-share.png',
        ], null);
    }
}
