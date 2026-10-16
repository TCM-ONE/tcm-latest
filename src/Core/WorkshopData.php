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
                'title'             => 'Python Starter Workshop | Advanced Live Coding & Web Scraping',
                'short_title'       => 'Python Starter Workshop',
                'tagline'           => 'Master Advanced Python, Web Scraping with BeautifulSoup, API Integration & Automation Scripts.',
                'description'       => 'Join the 3-day Python Workshop from TCM One for ₹99. Master advanced Python logic, Web Scraping with BeautifulSoup, REST APIs, and automated data extraction.',
                'full_description'  => 'Take your Python skills beyond the basics. Master object-oriented programming, live REST API integration, web scraping with BeautifulSoup, pagination handling, and automated data pipelines with live mentor support.',
                'price'             => 99.00,
                'original_price'    => 499.00,
                'duration'          => '3 Days',
                'schedule'          => 'Evening live sessions',
                'starts_at'         => 'Next Batch Coming Soon',
                'mode'              => 'Live Online Cohort',
                'category'          => 'Python Programming',
                'icon'              => 'bi-filetype-py',
                'badge'             => 'Advanced, Hands-On Learning',
                'curriculum'        => [
                    [
                        'day' => 'Day 01',
                        'title' => 'Advanced Python Essentials, OOP & REST API Integration',
                        'topics' => [
                            'Advanced Python Constructs: List & Dict comprehensions, lambda expressions, *args/**kwargs, and generators',
                            'Object-Oriented Programming (OOP): Classes, methods, encapsulation, dataclasses, and modular code architecture',
                            'Working with External APIs: Mastering the requests library, HTTP GET/POST, headers, query params & status codes',
                            'Robust Error Handling & Defensive Coding: try-except-finally blocks, custom exceptions, and JSON data parsing',
                            'Hands-on Lab: Build an Automated Real-Time Weather & Currency Rate Extractor via Live REST APIs'
                        ]
                    ],
                    [
                        'day' => 'Day 02',
                        'title' => 'Web Scraping Mastery with BeautifulSoup & Data Harvesting',
                        'topics' => [
                            'Web Architecture & Scraping Fundamentals: Inspecting DOM trees, HTML tags, CSS Selectors, and XPath patterns',
                            'Parsing HTML with BeautifulSoup4: Navigating elements, extracting nested attributes, text, and tables',
                            'Overcoming Real-World Scraping Barriers: Custom User-Agents, request throttling, and session management',
                            'Multi-Page Crawling & Pagination: Automating pagination loops to harvest multi-page catalogs cleanly',
                            'Hands-on Lab: Build an Automated E-Commerce / Tech Job Scraper exporting structured datasets into CSV & JSON'
                        ]
                    ],
                    [
                        'day' => 'Day 03',
                        'title' => 'Dynamic Scraping Concepts, Data Pipelines & Automated Alert Bots',
                        'topics' => [
                            'Handling Dynamic & JavaScript-Heavy Sites: Inspecting hidden Network XHR/Fetch endpoints & Selenium/Playwright overview',
                            'Data Cleaning & Pipeline: Cleaning scraped raw records with Python & Pandas, handling missing data, and file exports',
                            'Automated Script Scheduling & Alerts: Automating scrapers and dispatching real-time Telegram / Discord / Email alerts',
                            'Capstone Project: Build a Production-Ready Price Tracker & Stock Alert Bot that monitors live websites and triggers alerts',
                            'Next Steps & Ecosystem: Introduction to Scrapy, async scraping with aiohttp, and building a high-value freelance portfolio'
                        ]
                    ],
                ],
                'highlights'        => [
                    '3 intensive live coding sessions covering Advanced Python, APIs & Web Scraping',
                    'Hands-on web scraping with BeautifulSoup4 — extract live data from real-world websites',
                    'Reverse-engineer web APIs and scrape multi-page dynamic catalogs cleanly',
                    'Capstone Project: Build an automated live Price Tracker and alert bot',
                    'Export structured data into CSV/Excel pipelines with Pandas and standard libraries',
                    'Verified Certificate of Completion & GitHub repository code reviews',
                ],
                'target_audience'   => [
                    'College Students & Developers: Wanting to master Advanced Python, Web Automation & Scraping for real projects.',
                    'Aspiring Data & Backend Engineers: Learn how to harvest web data, parse APIs, and build automated data pipelines.',
                    'Freelancers & Automation Enthusiasts: Extract real-world leads, prices, and catalogs with production-ready scraping bots.',
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

            'data-analysis-workshop' => [
                'slug'              => 'data-analysis-workshop',
                'title'             => 'Data Analysis Masterclass: Excel, SQL, Python & Power BI | Dussehra Special',
                'short_title'       => 'Data Analysis Workshop',
                'tagline'           => 'Master Excel, SQL, Python & Power BI in 2 weeks of live classes with real-world business datasets.',
                'description'       => 'Join the 2-week Data Analysis Workshop from TCM One for ₹999 (Dussehra Special). Learn Excel analytics, SQL querying, Python Pandas, and Power BI dashboards with live mentor guidance.',
                'full_description'  => 'Kickstart your data career with our intensive 2-Week Dussehra Special Data Analysis Workshop. Over 2 weeks of live hands-on classes, you\'ll learn end-to-end data processing: from raw spreadsheets in Microsoft Excel, to relational database querying with SQL, data manipulation with Python & Pandas, and creating interactive executive dashboards in Power BI.',
                'price'             => 999.00,
                'original_price'    => 2499.00,
                'duration'          => '2 Weeks',
                'schedule'          => 'Evening live sessions (Mon–Fri)',
                'starts_at'         => 'Dussehra Special Cohort Starting Soon',
                'mode'              => 'Live Online Cohort',
                'category'          => 'Data Analytics',
                'icon'              => 'bi-bar-chart-line',
                'badge'             => 'Dussehra Special &middot; 2 Weeks Live',
                'curriculum'        => [
                    [
                        'day' => 'Day 01',
                        'title' => 'Foundations of Data Analysis & Modern Excel Essentials',
                        'topics' => [
                            'Introduction to the data analysis lifecycle: Question, Wrangle, Explore, Analyze & Report',
                            'Excel environment, cell references, data hygiene, and essential keyboard shortcuts',
                            'Data cleaning essentials: Text-to-Columns, Remove Duplicates, TRIM, CLEAN, and Flash Fill',
                            'Hands-on Lab: Cleaning messy raw customer leads and normalizing irregular formats'
                        ]
                    ],
                    [
                        'day' => 'Day 02',
                        'title' => 'Advanced Excel for Business Analytics & Pivot Tables',
                        'topics' => [
                            'Modern lookup formulas: XLOOKUP vs VLOOKUP/INDEX-MATCH and error shielding with IFERROR',
                            'Logical and conditional functions: SUMIFS, COUNTIFS, AVERAGEIFS, and nested IFs',
                            'Pivot Tables & Pivot Charts: Grouping dates, calculated fields, slicers, and timelines',
                            'Hands-on Lab: Constructing a dynamic sales performance scorecard with KPIs in Excel'
                        ]
                    ],
                    [
                        'day' => 'Day 03',
                        'title' => 'Relational Database Architecture & SQL Query Fundamentals',
                        'topics' => [
                            'RDBMS concepts: Tables, primary keys, foreign keys, and relational schema diagrams',
                            'Basic SQL query anatomy: SELECT, DISTINCT, WHERE filtering, operators, and pattern matching (LIKE)',
                            'Sorting, pagination, and limiting results: ORDER BY, ASC/DESC, LIMIT & OFFSET',
                            'Hands-on Lab: Querying a live multi-table retail database to answer business questions'
                        ]
                    ],
                    [
                        'day' => 'Day 04',
                        'title' => 'Advanced SQL Queries: Joins, Aggregations & Subqueries',
                        'topics' => [
                            'Multi-table joins: INNER JOIN, LEFT JOIN, RIGHT JOIN, and self-joins with clear real-world examples',
                            'Aggregate functions: COUNT, SUM, AVG, MIN, MAX with GROUP BY and HAVING clauses',
                            'Subqueries and Common Table Expressions (CTEs) for multi-step logic',
                            'Hands-on Lab: Writing complex queries to extract top-spending customers and underperforming product categories'
                        ]
                    ],
                    [
                        'day' => 'Day 05',
                        'title' => 'Week 1 Hands-on Project Lab: E-Commerce Analytics with SQL',
                        'topics' => [
                            'Connecting realistic e-commerce datasets: Customers, Orders, Order_Items, and Products',
                            'Cohort retention analysis and month-over-month revenue growth tracking in SQL',
                            'Exporting query datasets into structured CSV and formatting presentation outputs',
                            'Deliverable Review: Mentored feedback on SQL query efficiency and output accuracy'
                        ]
                    ],
                    [
                        'day' => 'Day 06',
                        'title' => 'Python for Data Analysis & Jupyter / Colab Environment',
                        'topics' => [
                            'Why Python for Data: Ecosystem overview, Google Colab and Jupyter Notebook workflows',
                            'Core Python refresher for data analysts: Lists, dicts, list comprehensions, and functions',
                            'NumPy arrays: Vectorized operations, broadcasting, math aggregations, and performance edge',
                            'Hands-on Lab: Manipulating multi-dimensional arrays and executing mathematical analysis in Colab'
                        ]
                    ],
                    [
                        'day' => 'Day 07',
                        'title' => 'Data Wrangling & Transformation with Pandas',
                        'topics' => [
                            'Pandas Series & DataFrames: Reading CSV, Excel, and JSON datasets effortlessly',
                            'Inspecting data: info(), describe(), shape, head(), tail(), and memory usage',
                            'Handling missing values, data type casting, column renaming, and string manipulation',
                            'Filtering rows, subsetting columns, and applying lambda/custom transformations with apply()',
                            'Hands-on Lab: Cleaning a real-world messy dataset with 50,000+ consumer transactions'
                        ]
                    ],
                    [
                        'day' => 'Day 08',
                        'title' => 'Exploratory Data Analysis (EDA) & Data Visualisation',
                        'topics' => [
                            'Principles of visual storytelling: Choosing the right chart type for your data narrative',
                            'Matplotlib fundamentals: Figures, axes, line plots, histograms, and bar charts',
                            'Seaborn high-level statistical plots: Box plots, scatter plots, violin plots, and correlation heatmaps',
                            'Hands-on Lab: Uncovering hidden sales patterns and outlier anomalies with visual EDA'
                        ]
                    ],
                    [
                        'day' => 'Day 09',
                        'title' => 'Business Intelligence & Interactive Dashboards with Power BI',
                        'topics' => [
                            'Power BI Desktop introduction: Connecting data sources (Excel, SQL, CSV) and Power Query ETL',
                            'Data modeling essentials: Star schema, relationships, cardinality, and filter direction',
                            'DAX basics: CALCULATE, SUM, RELATED, DIVIDE, and creating custom calculated measures',
                            'Building interactive dashboards: Cards, donut charts, matrix visuals, cross-filtering, and drill-throughs',
                            'Hands-on Lab: Designing a professional executive-ready Power BI dashboard from scratch'
                        ]
                    ],
                    [
                        'day' => 'Day 10',
                        'title' => 'Capstone Project Lab: End-to-End Analytics & Career Roadmap',
                        'topics' => [
                            'End-to-End Capstone: Clean raw business data with Excel & Python, query metrics with SQL, and visualize in Power BI',
                            'Executive presentation skills: Communicating insights clearly to non-technical stakeholders',
                            'Building an impressive Data Analytics GitHub repository and portfolio showcase',
                            'Resume optimization for Data Analyst & Business Analyst roles + Interview preparation tips',
                            'Capstone presentation & issuance of Verified Certificate of Completion'
                        ]
                    ],
                ],
                'highlights'        => [
                    '2 weeks of intensive live classes covering Excel, SQL, Python, and Power BI',
                    'Master Advanced Excel: Formulas, XLOOKUP, Pivot Tables, and automated data cleaning',
                    'Write production SQL queries with complex multi-table joins and aggregations',
                    'Data manipulation and exploratory analysis with Python, Pandas, and Seaborn',
                    'Build and publish interactive executive dashboards using Microsoft Power BI',
                    'Dussehra Special Festive Cohort with mentor feedback, doubt clearing, and verified certificate',
                ],
                'target_audience'   => [
                    'College Students & Freshers: Build job-ready Data Analyst and Business Analyst portfolio projects.',
                    'Working Professionals: Transition into analytics, business reporting, and data-driven decision making.',
                    'Tech & Non-Tech Learners: Anyone looking to master data tools without heavy mathematical prerequisite.',
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
            'python'                    => 'python-starter-workshop',
            'python-starter'            => 'python-starter-workshop',
            'python-workshop'           => 'python-starter-workshop',
            'website'                   => 'frontend-website-challenge-7-days',
            'website-building'          => 'frontend-website-challenge-7-days',
            'web-development'           => 'frontend-website-challenge-7-days',
            'frontend'                  => 'frontend-website-challenge-7-days',
            'ai'                        => 'ai-tools-everyday-work-workshop',
            'ai-tools'                  => 'ai-tools-everyday-work-workshop',
            'ai-agentic-coding'         => 'ai-tools-everyday-work-workshop',
            'ai-workshop'               => 'ai-tools-everyday-work-workshop',
            'career'                    => 'find-your-tech-career-path-workshop',
            'career-path'               => 'find-your-tech-career-path-workshop',
            'dsa'                       => 'find-your-tech-career-path-workshop',
            'dsa-starter'               => 'find-your-tech-career-path-workshop',
            'dsa-workshop'              => 'find-your-tech-career-path-workshop',
            'data'                      => 'data-analysis-workshop',
            'data-analysis'             => 'data-analysis-workshop',
            'data-analytics'            => 'data-analysis-workshop',
            'data-analytics-workshop'   => 'data-analysis-workshop',
            'data-analysis-workshop'    => 'data-analysis-workshop',
            'dussehra-special'          => 'data-analysis-workshop',
            'dashara-special'           => 'data-analysis-workshop',
            'dussehra-workshop'         => 'data-analysis-workshop',
        ];

        return $map[$slug] ?? $slug;
    }
}
