<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * Centralized repository of TCM One Workshops.
 *
 * Provides static workshop definitions, search, and validation without requiring
 * separate database migrations.
 */
final class WorkshopData
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'python-starter-workshop' => [
                'slug'              => 'python-starter-workshop',
                'title'             => 'Python Starter Workshop | 3-Day Live Coding',
                'short_title'       => 'Python Starter Workshop',
                'tagline'           => 'Start Python programming with practical beginner-friendly live sessions.',
                'description'       => 'Join the 3-day Python Starter Workshop from TCM One for ₹99. Learn Python basics, logic building, and your first practical mini-project.',
                'full_description'  => 'Start your programming journey with Python through three practical live sessions. Learn variables, control flow, functions, and simple problem-solving with guided mentor support.',
                'price'             => 99.00,
                'original_price'    => 499.00,
                'duration'          => '3 Days',
                'schedule'          => 'Evening live sessions',
                'starts_at'         => 'Next Batch Coming Soon',
                'mode'              => 'Live Online Cohort',
                'category'          => 'Python Programming',
                'icon'              => 'bi-filetype-py',
                'badge'             => 'Beginner Workshop',
                'curriculum'        => [
                    ['day' => 'Day 01', 'title' => 'Python setup, variables, data types and input/output'],
                    ['day' => 'Day 02', 'title' => 'Conditions, loops and beginner logic-building exercises'],
                    ['day' => 'Day 03', 'title' => 'Functions, mini-project build and next learning roadmap'],
                ],
                'highlights'        => [
                    '3 practical live beginner sessions',
                    'Learn Python syntax through guided exercises',
                    'Build your first small Python project',
                    'Mentor support for common beginner doubts',
                    'Certificate guidance after participation',
                ],
            ],

            'frontend-website-challenge-7-days' => [
                'slug'              => 'frontend-website-challenge-7-days',
                'title'             => '7-Day Frontend Website Challenge | TCM One',
                'short_title'       => '7-Day Frontend Website Challenge',
                'tagline'           => 'Build and publish a responsive website with HTML, CSS and JavaScript.',
                'description'       => 'Join the 7-Day Frontend Website Challenge from TCM One for ₹999. Build a responsive website with HTML, CSS, JavaScript and mentor guidance.',
                'full_description'  => 'Build and publish your first responsive website in a structured 7-day live challenge. Learn semantic HTML, modern CSS layout, JavaScript basics, and deployment with practical mentor guidance.',
                'price'             => 999.00,
                'original_price'    => 1999.00,
                'duration'          => '7 Days',
                'schedule'          => 'Evening live sessions',
                'starts_at'         => 'Next Batch Coming Soon',
                'mode'              => 'Live Online Cohort',
                'category'          => 'Frontend Development',
                'icon'              => 'bi-window',
                'badge'             => 'Project Challenge',
                'curriculum'        => [
                    ['day' => 'Day 01', 'title' => 'How the Web Works, HTML5 Skeleton & Semantic Tags'],
                    ['day' => 'Day 02', 'title' => 'Typography, Forms, Inputs & User Experience Basics'],
                    ['day' => 'Day 03', 'title' => 'CSS Fundamentals, Box Model, Borders & Spacing'],
                    ['day' => 'Day 04', 'title' => 'Responsive Design, Media Queries & Flexbox Mastery'],
                    ['day' => 'Day 05', 'title' => 'Modern UI Polish, Hero Sections & Navigation Bars'],
                    ['day' => 'Day 06', 'title' => 'Capstone Project: Complete Responsive Personal Website'],
                    ['day' => 'Day 07', 'title' => 'Custom Domain, Git Setup & Live Cloud Deployment'],
                ],
                'highlights'        => [
                    '7 practical live frontend sessions',
                    'Build and publish a responsive website',
                    'Beginner-friendly HTML, CSS and JavaScript practice',
                    'Portfolio-ready final project',
                    'Private cohort support and mentor Q&A',
                ],
            ],

            'ai-tools-everyday-work-workshop' => [
                'slug'              => 'ai-tools-everyday-work-workshop',
                'title'             => 'AI Tools for Everyday Work | TCM One Workshop',
                'short_title'       => 'AI Tools for Everyday Work',
                'tagline'           => 'Use modern AI tools for study, productivity, research and daily work.',
                'description'       => 'Learn practical AI tools for everyday study and work with TCM One. Beginner-friendly workshop covering prompting, research and productivity workflows.',
                'full_description'  => 'Learn how to use AI tools responsibly for everyday study and work. Practice prompt writing, research summarisation, document workflows, and simple automation examples.',
                'price'             => 199.00,
                'original_price'    => 499.00,
                'duration'          => '2 Days',
                'schedule'          => 'Evening live sessions',
                'starts_at'         => 'Next Batch Coming Soon',
                'mode'              => 'Live Online Cohort',
                'category'          => 'AI Tools',
                'icon'              => 'bi-robot',
                'badge'             => 'Coming Soon',
                'curriculum'        => [
                    ['day' => 'Day 01', 'title' => 'AI basics, prompt writing, research and study workflows'],
                    ['day' => 'Day 02', 'title' => 'Document, presentation, coding and productivity examples'],
                ],
                'highlights'        => [
                    'Practical workflows for students and professionals',
                    'Prompt writing patterns for repeatable results',
                    'Examples for research, documents and coding help',
                    'Guidance on responsible and accurate AI use',
                ],
            ],

            'find-your-tech-career-path-workshop' => [
                'slug'              => 'find-your-tech-career-path-workshop',
                'title'             => 'Find Your Tech Career Path | TCM One Workshop',
                'short_title'       => 'Find Your Tech Career Path',
                'tagline'           => 'Understand web, data, AI, cloud and DSA paths before choosing a course.',
                'description'       => 'A beginner-friendly TCM One workshop to help students compare tech career paths and choose the right learning roadmap.',
                'full_description'  => 'Explore practical technology career paths before committing to a course. Compare web development, Python, data analytics, AI, cloud, DSA and portfolio-building roadmaps.',
                'price'             => 0.00,
                'original_price'    => 499.00,
                'duration'          => '1 Day',
                'schedule'          => 'Evening live session',
                'starts_at'         => 'Next Batch Coming Soon',
                'mode'              => 'Live Online Cohort',
                'category'          => 'Career Guidance',
                'icon'              => 'bi-compass',
                'badge'             => 'Coming Soon',
                'curriculum'        => [
                    ['day' => 'Session', 'title' => 'Career paths, skills, project examples and beginner roadmap planning'],
                ],
                'highlights'        => [
                    'Compare major tech learning paths clearly',
                    'Understand beginner prerequisites for each path',
                    'See examples of projects and portfolio direction',
                    'Choose next steps based on your current level',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $slug): ?array
    {
        $all = self::all();
        return $all[$slug] ?? null;
    }

    /**
     * Aliases for friendly matching.
     */
    public static function resolveSlug(string $slug): string
    {
        $map = [
            'python'            => 'python-starter-workshop',
            'python-starter'    => 'python-starter-workshop',
            'python-workshop'   => 'python-starter-workshop',
            'website'           => 'frontend-website-challenge-7-days',
            'website-building'  => 'frontend-website-challenge-7-days',
            'web-development'   => 'frontend-website-challenge-7-days',
            'frontend'          => 'frontend-website-challenge-7-days',
            'ai'                => 'ai-tools-everyday-work-workshop',
            'ai-tools'          => 'ai-tools-everyday-work-workshop',
            'ai-agentic-coding' => 'ai-tools-everyday-work-workshop',
            'ai-workshop'       => 'ai-tools-everyday-work-workshop',
            'career'            => 'find-your-tech-career-path-workshop',
            'career-path'       => 'find-your-tech-career-path-workshop',
            'dsa'               => 'find-your-tech-career-path-workshop',
            'dsa-starter'       => 'find-your-tech-career-path-workshop',
            'dsa-workshop'      => 'find-your-tech-career-path-workshop',
        ];

        return $map[$slug] ?? $slug;
    }
}
