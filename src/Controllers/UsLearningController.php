<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;

final class UsLearningController extends Controller
{
    public const PATH = '/online-coding-courses-usa';

    private const TOPICS = [
        'python' => [
            'label' => 'Python programming',
            'heading' => 'Live online Python courses for U.S. learners',
            'description' => 'Compare beginner Python classes and a Python for AI and ML course. Explore practical scripts, coding exercises, and live cohort details for U.S. learners.',
            'intro' => 'Start with Python programming fundamentals, then choose whether to use the language for everyday automation, data work, or machine learning. TCM One offers live online cohorts you can join from the United States.',
            'courses' => [
                ['python-programming-automation', 'Python Programming', 'Start here if you are new to coding. Practice variables, loops, functions, object-oriented programming, and working with files.'],
                ['python-for-ai-ml', 'Python for AI & ML', 'Choose this route if your goal is data or machine learning. Review the curriculum for NumPy, Pandas, visualization, and the math used in introductory AI.'],
            ],
            'practiceHeading' => 'A beginner Python project to try',
            'practice' => 'Write a script that reads a CSV file, skips invalid rows, and writes a clean summary. Start with a small file and split validation and reporting into separate functions. Run it with a missing file and an empty file to see how it handles errors. This is a sample project you can use to judge whether you are ready for more advanced automation.',
            'faq' => [
                ['Which online Python course should I start with?', 'Start with Python Programming if you have never coded. If your goal is model building, learn to work with Python functions and datasets before moving into a full machine learning course.'],
                ['What should I ask before joining live Python classes?', 'Confirm whether the instructor will review your code, what software you need to install, and how you can catch up when a session overlaps with school or work.'],
            ],
        ],
        'data-analysis' => [
            'label' => 'Data analysis',
            'heading' => 'Live online data analysis courses for U.S. learners',
            'description' => 'Compare live data analytics courses with Excel, SQL, Python, and Power BI. Choose a path for reporting, dashboard design, or analysis with Pandas.',
            'intro' => 'Learn to clean data, answer business questions with SQL, and present your findings. Compare courses based on whether you want to use Python for analysis or build reports and dashboards in Power BI.',
            'courses' => [
                ['data-analysis', 'Data Analysis', 'Work with Excel, SQL, Python and Pandas. Explore data cleaning, joins, aggregation, visualization, and KPI reporting.'],
                ['data-analytics-with-excel-sql-power-bi-python', 'Data Analytics with Excel, SQL, Power BI & Python', 'Choose this route if dashboards are your priority. Review Power Query, data modeling, DAX basics, and the portfolio case study in the syllabus.'],
            ],
            'practiceHeading' => 'From a sales spreadsheet to a dashboard',
            'practice' => 'Start with a sales file and a question such as which product categories grew quarter over quarter. Clean dates and missing values, use SQL to summarize results by category, and build a dashboard that lets a reader filter by quarter. Add a short note explaining the metric and any assumptions, so another person can reproduce your result.',
            'faq' => [
                ['Should I learn Excel and SQL before Python for data analysis?', 'Excel lets you inspect the data and spot problems quickly. SQL helps you join tables and summarize records. Python and Pandas are useful once you need to repeat the same cleaning and analysis steps across files.'],
                ['What should I check before choosing a Power BI course?', 'Check that your computer can run the required tools, ask which licenses you need, and confirm whether the cohort includes dashboard feedback and an exportable project.'],
            ],
        ],
        'ai-machine-learning' => [
            'label' => 'AI & machine learning',
            'heading' => 'Live online AI and machine learning courses for U.S. learners',
            'description' => 'Compare online AI and machine learning courses with Python, from model-building fundamentals to deep learning and deployment. Check prerequisites and cohort times.',
            'intro' => 'Compare instructor-led courses based on what you can already do with Python and what you want to build. The fundamentals course introduces machine learning models. The masterclass also covers deep learning, computer vision, language applications, and deployment.',
            'courses' => [
                ['ai-machine-learning-fundamentals', 'AI & Machine Learning Fundamentals', 'Start with core AI concepts and machine learning algorithms. Check the Python exercises and expected preparation before enrolling.'],
                ['data-science-machine-learning-foundations', 'Data Science & Machine Learning Foundations', 'Choose this path for data preparation, exploratory analysis, regression, classification, clustering, and model evaluation.'],
                ['artificial-intelligence-machine-learning', 'AI & Machine Learning Masterclass', 'For learners ready for a broader curriculum, compare the modules on PyTorch, computer vision, natural language processing, and deploying a model with FastAPI and Docker.'],
            ],
            'practiceHeading' => 'What a first machine learning project should show',
            'practice' => 'Pick a prediction question and define what a useful result would look like. Split the data before training, compare the model against a simple baseline, and evaluate it on examples it has never seen. Keep a short note on where the model fails. The ability to explain those choices matters when comparing project-based courses.',
            'faq' => [
                ['Do I need Python before taking an AI and machine learning course?', 'Python functions, loops, and basic work with datasets make the model-building exercises easier. Review the Python learning path if you are new to programming, and confirm the requirements for the course you choose.'],
                ['How is machine learning training different from an AI tools course?', 'Machine learning courses teach you to prepare data, train a model, and test its performance. An AI tools course typically focuses on using existing assistants for tasks such as research, writing, and workflow automation.'],
            ],
        ],
    ];

    public function show(array $params = []): void
    {
        $slug = $params['topic'] ?? null;
        $topic = self::TOPICS[$slug] ?? null;
        if ($slug !== null && $topic === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Page Not Found'], null);
            return;
        }

        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $this->view('public/international/us-learners', [
            'title' => ($topic['heading'] ?? 'Online Coding Courses for U.S. Learners') . ' | TCM One',
            'description' => $topic['description'] ?? 'Compare live online Python, data analysis, and AI and machine learning courses. Check course projects and cohort times before enrolling from the United States.',
            'canonical' => $appUrl . self::PATH . ($slug === null ? '' : '/' . $slug),
            'ogImage' => $appUrl . '/assets/tcm-og-share.png',
            'appUrl' => $appUrl,
            'hubPath' => self::PATH,
            'topics' => self::TOPICS,
            'topic' => $topic,
            'topicSlug' => $slug,
        ], null);
    }
}
