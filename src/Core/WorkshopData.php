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
                    [
                        'day' => 'Day 01',
                        'title' => 'Python Environment Setup, Core Syntax & Variables',
                        'topics' => [
                            'Installing Python 3.12 & VS Code setup with essential extensions',
                            'Mental models of Python execution and script running',
                            'Variables, primitive data types (int, float, string, boolean)',
                            'User inputs, type casting and formatted string outputs (f-strings)',
                            'Hands-on Lab: Build an interactive terminal currency & unit converter'
                        ]
                    ],
                    [
                        'day' => 'Day 02',
                        'title' => 'Control Flow, Logic Building & Iteration Loops',
                        'topics' => [
                            'Comparison and logical operators (if, elif, else conditions)',
                            'While loops and For loops with range() iterations',
                            'Python Lists & Dictionaries — storing and accessing collections',
                            'Defensive input handling, string transformations, and clean code principles',
                            'Hands-on Lab: Build a multi-round Number Guessing & Logic Game'
                        ]
                    ],
                    [
                        'day' => 'Day 03',
                        'title' => 'Functions, Automation Scripts & Next Steps',
                        'topics' => [
                            'Defining custom functions with arguments, default parameters and return values',
                            'Working with standard libraries (os, sys, time, random)',
                            'File handling: Reading, parsing, and writing text/CSV files on disk',
                            'Capstone Project: Build an automated desktop file organizer script',
                            'Career roadmap: Python for Web (Django/FastAPI) vs Data Science & AI'
                        ]
                    ],
                ],
                'highlights'        => [
                    '3 practical live beginner sessions with 1-on-1 mentor guidance',
                    'Learn Python syntax step-by-step through guided coding exercises',
                    'Build 2 real terminal utilities and an automated desktop file organizer',
                    'Zero prior coding experience required — starts with fundamental mental models',
                    'Verified Certificate of Completion & GitHub repository code reviews',
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
                    [
                        'day' => 'Day 01',
                        'title' => 'How the Web Works & Semantic HTML5 Architecture',
                        'topics' => [
                            'Client-Server request-response cycle and browser DOM parsing',
                            'HTML5 document structure: header, nav, main, section, article, footer',
                            'SEO fundamentals: Meta tags, Open Graph previews, and accessible headings',
                            'Hands-on Lab: Structuring an accessible developer portfolio skeleton'
                        ]
                    ],
                    [
                        'day' => 'Day 02',
                        'title' => 'Forms, Media Elements & Interactive Web Inputs',
                        'topics' => [
                            'Form inputs, labels, placeholders, pattern validations, and accessibility attributes',
                            'Embedding images, responsive SVGs, and audio/video players',
                            'Table design and structured data presentation',
                            'Hands-on Lab: Crafting a high-converting lead capture & feedback form'
                        ]
                    ],
                    [
                        'day' => 'Day 03',
                        'title' => 'CSS3 Fundamentals, Box Model & Modern Typography',
                        'topics' => [
                            'CSS Selectors, specificity hierarchy, and cascading inheritance rules',
                            'The Box Model: Content, padding, border, and margin collapse rules',
                            'Google Fonts, fluid font sizing, line-heights, and visual hierarchy',
                            'Hands-on Lab: Styling a sleek, high-contrast monochrome design system'
                        ]
                    ],
                    [
                        'day' => 'Day 04',
                        'title' => 'Flexbox Layouts & Responsive Media Queries',
                        'topics' => [
                            'CSS Flexbox: justify-content, align-items, flex-wrap, and gap properties',
                            'Mobile-first responsive design strategies and viewport meta settings',
                            'Custom breakpoints for mobile phones, tablets, and desktop displays',
                            'Hands-on Lab: Building a fluid, mobile-responsive navbar and multi-column grid'
                        ]
                    ],
                    [
                        'day' => 'Day 05',
                        'title' => 'CSS Grid, Transitions & Micro-Interactions',
                        'topics' => [
                            'CSS Grid: repeat(), minmax(), grid-template-columns, and card layouts',
                            'Smooth CSS transitions, transforms (hover states, scale, translate)',
                            'Accessible focus rings, active states, and custom UI components',
                            'Hands-on Lab: Building interactive pricing tables and project showcase cards'
                        ]
                    ],
                    [
                        'day' => 'Day 06',
                        'title' => 'JavaScript DOM Manipulation & Dynamic Interactivity',
                        'topics' => [
                            'Connecting JavaScript to HTML: querySelector, addEventListener, and classList',
                            'Building an interactive mobile hamburger navigation drawer',
                            'Form validation with real-time feedback and modal popup triggers',
                            'Hands-on Lab: Wiring interactive modals and dark/light mode toggles'
                        ]
                    ],
                    [
                        'day' => 'Day 07',
                        'title' => 'Git Version Control, Cloud Deployment & Live URL Launch',
                        'topics' => [
                            'Git essentials: git init, git add, git commit, and pushing to GitHub',
                            'Deploying to live production hosting via Vercel / GitHub Pages',
                            'Setting up custom domain records and testing mobile responsiveness live',
                            'Capstone Deliverable: Live portfolio URL published to show recruiters'
                        ]
                    ],
                ],
                'highlights'        => [
                    '7 intensive live evening sessions with real-time code reviews',
                    'Build, test, and launch a complete responsive website from scratch',
                    'Master semantic HTML5, modern CSS3 Flexbox/Grid, and core JavaScript DOM',
                    'Publish live to GitHub Pages / Vercel with your own live shareable link',
                    'Private cohort Discord/WhatsApp group with daily doubt clearing',
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
                    [
                        'day' => 'Day 01',
                        'title' => 'Generative AI Foundations, Prompt Engineering & Research Workflows',
                        'topics' => [
                            'How Large Language Models (LLMs) work: Tokens, context windows, and hallucination guardrails',
                            'Chain-of-Thought prompting, persona framing, and few-shot instruction templates',
                            'Synthesizing dense research papers, technical documentation, and textbook chapters',
                            'Hands-on Lab: Crafting custom prompt frameworks for fast study notes & exam preparation'
                        ]
                    ],
                    [
                        'day' => 'Day 02',
                        'title' => 'Workplace Productivity, Code Generation & Automated Workflows',
                        'topics' => [
                            'Automating professional communications: Crisp emails, proposal drafting, and executive summaries',
                            'AI coding assistants (Claude, ChatGPT, GitHub Copilot) for debugging and writing regex/SQL',
                            'Data analysis and visualization from raw spreadsheets using AI assistants',
                            'Hands-on Lab: Building a repeatable personal daily workflow pipeline'
                        ]
                    ],
                ],
                'highlights'        => [
                    '2 actionable live sessions covering practical modern AI workflows',
                    'Prompt templates and frameworks for repeatable, high-accuracy results',
                    'Live demonstrations for literature review, coding assistance, and spreadsheets',
                    'Essential principles for verifying AI accuracy and avoiding costly hallucination errors',
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
                    [
                        'day' => 'Session 01',
                        'title' => 'Landscape of Modern Tech Careers: Web, Data, Cloud & AI',
                        'topics' => [
                            'Full Stack Web vs Data Analytics vs Cloud DevOps: Skills, daily tasks, and market demand',
                            'Why DSA matters for product companies and how to structure problem-solving practice',
                            'Building portfolio proof: Why GitHub repositories beat resume bullet points',
                            'Interactive Q&A: 1-on-1 path recommendation based on your year of study & background'
                        ]
                    ],
                ],
                'highlights'        => [
                    'Clear, non-biased breakdown of major tech tracks and hiring expectations',
                    'Understand prerequisites and realistic timelines for each specialization',
                    'Live review of industry-standard student portfolio examples',
                    'Personalized question-and-answer session with industry engineering mentors',
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
