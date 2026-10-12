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
            'website-building' => [
                'slug'              => 'website-building',
                'title'             => 'Build Your First Website | 7-Day Live Workshop',
                'short_title'       => 'Build Your First Website',
                'tagline'           => 'Learn HTML, CSS & web development fundamentals with TCM One.',
                'description'       => 'Learn HTML, CSS and website development fundamentals with TCM One. Live beginner workshop for ₹999.',
                'full_description'  => 'Start your coding journey with an interactive, live 7-day workshop. Build a real, responsive website, learn modern CSS layout techniques (Flexbox & Grid), and deploy your site live to the web with personal mentor guidance.',
                'price'             => 999.00,
                'original_price'    => 1999.00,
                'duration'          => '7 Days',
                'schedule'          => '7:00 PM – 8:30 PM IST',
                'starts_at'         => 'Every Monday',
                'mode'              => 'Live Online Cohort',
                'category'          => 'Web Development',
                'icon'              => 'bi-window',
                'badge'             => 'Bestseller Workshop',
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
                    '7 Daily 90-Minute Interactive Live Sessions',
                    'Build & Deploy a Live Portfolio Website',
                    'Beginner Friendly — Zero Prior Coding Needed',
                    'Verified Certificate of Completion',
                    'Private WhatsApp Cohort & Mentor Q&A',
                ],
            ],

            'python-starter' => [
                'slug'              => 'python-starter',
                'title'             => 'Python Starter Workshop | 7-Day Live Coding',
                'short_title'       => 'Python Starter Workshop',
                'tagline'           => 'Master Python programming, automation & logic building from scratch.',
                'description'       => 'Learn Python programming fundamentals, automation and logic building from scratch with TCM One. 7-Day live beginner workshop for ₹999.',
                'full_description'  => 'Master core Python syntax, control flow, functions, and real-world scripting in 7 live sessions. Perfect for college students and non-IT beginners looking to build genuine coding logic.',
                'price'             => 999.00,
                'original_price'    => 1999.00,
                'duration'          => '7 Days',
                'schedule'          => '8:30 PM – 10:00 PM IST',
                'starts_at'         => 'Every Monday',
                'mode'              => 'Live Online Cohort',
                'category'          => 'Python Programming',
                'icon'              => 'bi-filetype-py',
                'badge'             => 'Popular Choice',
                'curriculum'        => [
                    ['day' => 'Day 01', 'title' => 'Python Environment Setup, Variables & Core Data Types'],
                    ['day' => 'Day 02', 'title' => 'Arithmetic, Logical Operators & Dynamic User Input'],
                    ['day' => 'Day 03', 'title' => 'Conditional Logic, If-Else Branching & Algorithmic Thinking'],
                    ['day' => 'Day 04', 'title' => 'Loops & Iterations (While, For, Nested Loops)'],
                    ['day' => 'Day 05', 'title' => 'Data Structures: Lists, Tuples & Dictionaries'],
                    ['day' => 'Day 06', 'title' => 'Modular Programming: Functions, Scope & Python Modules'],
                    ['day' => 'Day 07', 'title' => 'Capstone Automation Script Build & Certificate Award'],
                ],
                'highlights'        => [
                    '7 Daily Live Coding & Debugging Sessions',
                    'Build Real Automation & Data Processing Scripts',
                    'Solid Foundation for Data Science & Backend',
                    'Verified Certificate of Completion',
                    'Dedicated Mentor Support via WhatsApp',
                ],
            ],

            'ai-agentic-coding' => [
                'slug'              => 'ai-agentic-coding',
                'title'             => 'AI Tools & Agentic Coding Workshop | TCM One',
                'short_title'       => 'AI Tools & Agentic Coding',
                'tagline'           => 'Boost your coding speed by 10x using Gemini API, Cursor IDE & AI Agents.',
                'description'       => 'Boost coding speed by 10x using Gemini API, Cursor IDE, Claude, and automated AI coding agents with TCM One. Live workshop for ₹999.',
                'full_description'  => 'Step into the future of software development. Learn developer prompt engineering, Cursor IDE workflows, Google Gemini API integrations, and building autonomous agentic coding assistants.',
                'price'             => 999.00,
                'original_price'    => 1999.00,
                'duration'          => '7 Days',
                'schedule'          => '8:00 PM – 9:30 PM IST',
                'starts_at'         => 'Every Monday',
                'mode'              => 'Live Online Cohort',
                'category'          => 'AI & Tech Tools',
                'icon'              => 'bi-robot',
                'badge'             => 'Trending Workshop',
                'curriculum'        => [
                    ['day' => 'Day 01', 'title' => 'The AI Developer Paradigm: Cursor, Copilot & LLMs Overview'],
                    ['day' => 'Day 02', 'title' => 'Developer Prompt Engineering: System Prompts & Context Windows'],
                    ['day' => 'Day 03', 'title' => 'Cursor IDE Pro Workflows: Composer, Multi-file Edits & Diffing'],
                    ['day' => 'Day 04', 'title' => 'Google Gemini API Hands-on: Keys, Structured JSON & Tools'],
                    ['day' => 'Day 05', 'title' => 'Autonomous Coding Agents: Loops, Reflection & Tool Use'],
                    ['day' => 'Day 06', 'title' => 'Capstone Project: Build an End-to-End AI Web Application'],
                    ['day' => 'Day 07', 'title' => 'Deployment, Production Considerations & Future of Dev Careers'],
                ],
                'highlights'        => [
                    '7 Live Practical AI Engineering Sessions',
                    'Build and Ship a Production-Grade AI Web Tool',
                    'Master Modern Cursor IDE & Agentic Workflows',
                    'Verified Certificate of Completion',
                    'Exclusive AI Prompting Resource Pack',
                ],
            ],

            'dsa-starter' => [
                'slug'              => 'dsa-starter',
                'title'             => 'DSA & Problem Solving Starter | 7-Day Live Workshop',
                'short_title'       => 'DSA Problem Solving Starter',
                'tagline'           => 'Master coding logic, arrays, strings and interview problem-solving patterns.',
                'description'       => 'Crack coding rounds and master problem-solving patterns with TCM One. 7-Day live beginner DSA workshop for ₹999.',
                'full_description'  => 'Overcome the fear of Data Structures and Algorithms. Learn foundational pattern recognition, space-time complexity analysis, array and string tricks, and how to approach coding interview rounds with confidence.',
                'price'             => 999.00,
                'original_price'    => 1999.00,
                'duration'          => '7 Days',
                'schedule'          => '6:00 PM – 7:30 PM IST',
                'starts_at'         => 'Every Monday',
                'mode'              => 'Live Online Cohort',
                'category'          => 'Data Structures',
                'icon'              => 'bi-cpu',
                'badge'             => 'High Demand',
                'curriculum'        => [
                    ['day' => 'Day 01', 'title' => 'Time & Space Complexity (Big-O) Made Crystal Clear'],
                    ['day' => 'Day 02', 'title' => 'Array Traversals, Prefix Sums & Sliding Window Technique'],
                    ['day' => 'Day 03', 'title' => 'Two-Pointer Technique & Sorted Array Patterns'],
                    ['day' => 'Day 04', 'title' => 'String Manipulation, Anagrams & Palindrome Patterns'],
                    ['day' => 'Day 05', 'title' => 'Hashing, Frequency Maps & Hash Sets in Problem Solving'],
                    ['day' => 'Day 06', 'title' => 'Recursion Fundamentals & Divide-and-Conquer Mental Models'],
                    ['day' => 'Day 07', 'title' => 'Live Mock Interview Round, Strategy & Road Map Ahead'],
                ],
                'highlights'        => [
                    '7 Live Pattern-Based Problem Solving Sessions',
                    '25+ Selected Interview Problems Solved Live',
                    'Step-by-Step Logic Building from Scratch',
                    'Verified Certificate of Completion',
                    'Complete DSA Interview Revision Cheat Sheet',
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
            'python'            => 'python-starter',
            'python-workshop'   => 'python-starter',
            'website'           => 'website-building',
            'web-development'   => 'website-building',
            'ai'                => 'ai-agentic-coding',
            'ai-tools'          => 'ai-agentic-coding',
            'ai-workshop'       => 'ai-agentic-coding',
            'dsa'               => 'dsa-starter',
            'dsa-workshop'      => 'dsa-starter',
        ];

        return $map[$slug] ?? $slug;
    }
}
