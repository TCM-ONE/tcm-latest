<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Core\InterviewPrepCatalog;
use TCM\Core\LearnGuideCatalog;

/** Public interview practice built from the topics in the Learn library. */
final class InterviewPrepController extends Controller
{
    public function index(): void
    {
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $topics = InterviewPrepCatalog::forGuides(LearnGuideCatalog::all());
        $questionCount = array_sum(array_map(static fn (array $topic): int => count($topic['questions']), $topics));
        $canonical = $appUrl . '/learn/interview-preparation';
        $title = 'Coding Interview Questions with Answers & Examples | TCM One';
        $description = 'Practice common Python, Java, Spring Boot, HTML, CSS, Bootstrap, SCSS and GitHub Actions interview questions with clear explanations, code examples and simple ways to answer.';
        $faqQuestions = [];

        foreach ($topics as $topic) {
            foreach ($topic['questions'] as $item) {
                $faqQuestions[] = [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['answer']],
                ];
            }
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    '@id' => $canonical . '#interview-preparation',
                    'name' => $title,
                    'description' => $description,
                    'url' => $canonical,
                    'inLanguage' => 'en-IN',
                    'isAccessibleForFree' => true,
                    'numberOfItems' => $questionCount,
                    'hasPart' => array_map(static fn (array $topic): array => [
                        '@type' => 'LearningResource',
                        'name' => $topic['name'] . ' interview questions',
                        'learningResourceType' => 'Practice questions',
                    ], $topics),
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => $faqQuestions,
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $appUrl . '/'],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Learn', 'item' => $appUrl . '/learn'],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Interview Preparation', 'item' => $canonical],
                    ],
                ],
            ],
        ];

        $this->view('public/learn/interview-preparation', [
            'topics' => $topics,
            'questionCount' => $questionCount,
            'appUrl' => $appUrl,
            'canonical' => $canonical,
            'title' => $title,
            'description' => $description,
            'ogImage' => $appUrl . '/assets/tcm-og-share.png',
            'schema' => $schema,
        ], null);
    }
}
