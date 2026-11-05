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
                'intro' => 'Students and professionals in Bilaspur can join TCM One’s live online coding, Full Stack, Python, data analytics and AI courses from home or college. TCM One is based in Waraseoni, Madhya Pradesh; its Bilaspur campus is upcoming, while current TCM classes are delivered online.',
                'audience' => 'Bilaspur students, graduates and career changers comparing online programming, web development, Full Stack, data analytics or AI courses.',
                'localNote' => 'For local classroom computer education, JMK Computer Education Centre is TCM One’s listed institutional partner in Ashok Nagar, Sarkanda, Bilaspur. Its own profile lists options such as ADCA, PGDCA, Tally Advanced, programming and typing; contact JMK directly to confirm on-site availability, eligibility, fees and schedules. TCM One’s coding and technology cohorts are a separate online offering. Compare course prerequisites and current timings before enrolling.',
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
                'intro' => 'Join live online coding classes from Raipur with TCM One. Explore Full Stack and frontend development, Python, data analytics, AI and problem-solving programmes with instructor-led sessions and guided projects, accessible remotely across Chhattisgarh.',
                'audience' => 'Raipur college students, graduates and working professionals looking for structured online software development, data or AI learning.',
                'localNote' => 'TCM One’s Raipur programmes are online; there is no TCM classroom in Raipur. Compare the course outline, prerequisites, live session times and project work before enrolling. Choose frontend or Full Stack to practise building web applications; compare analytics and AI programmes for a different technical focus. For the current cohort and fee, use the course page or contact admissions.',
            ],
            'durg' => [
                'name' => 'Durg', 'region' => 'Chhattisgarh', 'focus' => 'coding, software development, data analytics and career preparation',
                'intro' => 'Learners in Durg, Bhilai and the wider Durg–Bhilai area can join TCM One’s live online coding classes in web development, Full Stack, Python, data analytics and AI. Instructor-led sessions and guided projects are available remotely across Chhattisgarh.',
                'audience' => 'Durg and Bhilai students, graduates and early-career professionals comparing online programming, software, analytics or AI courses.',
                'localNote' => 'TCM One’s Durg–Bhilai learning option is remote, with no local TCM classroom claimed. Compare prerequisites, weekly live-session times, project expectations and course fees before choosing a cohort. Programming and web foundations suit learners starting out; Full Stack, analytics and AI courses cover different next steps. Contact admissions to confirm the current schedule.',
            ],
            'chhattisgarh' => [
                'name' => 'Chhattisgarh', 'region' => 'Chhattisgarh', 'focus' => 'online coding, software development, data analytics and AI learning',
                'intro' => 'Learners across Chhattisgarh can join TCM One’s live online technology programmes remotely. Explore web and Full Stack development, Python, data analytics, AI and DSA courses, plus local information for Bilaspur, Raipur and Durg–Bhilai.',
                'audience' => 'Learners across Chhattisgarh comparing online coding courses and practical technology learning.',
                'localNote' => 'TCM One delivers these cohorts online and does not currently claim a physical campus in Chhattisgarh. Its Bilaspur campus is upcoming. For in-person computer education in Bilaspur, JMK Computer Education Centre is a separate institutional partner with its own course list and admissions; check availability and terms directly with JMK.',
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
            'title' => 'Online Coding Courses Across India | TCM One',
            'description' => 'Explore live online coding, Full Stack, Python, AI and data analytics courses for learners across India, including Chhattisgarh. Compare programmes and current schedules.',
        ], null);
    }

    public function show(array $params): void
    {
        $slug = strtolower((string) ($params['slug'] ?? ''));
        if ($slug === 'chhattisgarh') {
            $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
            $this->view('public/locations/chhattisgarh', [
                'appUrl' => $appUrl,
                'canonical' => $appUrl . '/locations/chhattisgarh',
                'title' => 'Online Coding Courses in Chhattisgarh | TCM One',
                'description' => 'Explore live online coding courses in Chhattisgarh: Full Stack, frontend, Python, data analytics, AI and DSA. Compare TCM One programmes, plus JMK’s Bilaspur classroom courses.',
                'ogImage' => $appUrl . '/assets/tcm-og-share.png',
                'diwaliOfferActive' => DiwaliOffer::isActive(),
            ], null);
            return;
        }
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
        if ($slug === 'bilaspur') {
            $title = 'Computer & Coding Courses in Bilaspur | TCM One + JMK';
            $description = 'Compare online Full Stack, Python, data and AI courses from TCM One with JMK partner classroom courses in Bilaspur, including ADCA, PGDCA, Tally and programming.';
        } elseif ($slug === 'raipur') {
            $title = 'Online Coding Classes in Raipur, Chhattisgarh | TCM One';
            $description = 'Join live online coding classes from Raipur. Compare Full Stack, frontend, Python, data analytics and AI courses, project work, fees and current cohort schedules.';
        } elseif ($slug === 'durg') {
            $title = 'Online Coding Classes in Durg–Bhilai | TCM One';
            $description = 'Explore online coding classes for Durg and Bhilai learners: Full Stack, frontend, Python, data analytics and AI with live sessions and guided projects.';
        }
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
