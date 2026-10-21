<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Core\LearnGuideCatalog;

/** Public self-study guides with version-aware technical references. */
final class LearnController extends Controller
{
    public function index(): void
    {
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $guides = LearnGuideCatalog::all();
        $title = 'Free Programming & Web Development Guides | TCM One';
        $description = 'Learn Python, HTML, CSS, SCSS, Bootstrap and Java from fundamentals to advanced topics with practical explanations, code examples and project ideas.';

        $this->view('public/learn/index', [
            'guides' => $guides,
            'appUrl' => $appUrl,
            'canonical' => $appUrl . '/learn',
            'title' => $title,
            'description' => $description,
            'ogImage' => $appUrl . '/assets/tcm-og-share.png',
        ], null);
    }

    /** @param array<string,string> $params */
    public function show(array $params): void
    {
        $slug = strtolower((string) ($params['slug'] ?? ''));
        $guides = LearnGuideCatalog::all();
        if (!isset($guides[$slug])) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Learning Guide Not Found | TCM One',
                'message' => 'This learning guide is unavailable. Browse the Learn library to choose another topic.',
            ], null);
            return;
        }

        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $guide = $guides[$slug];
        $title = $guide['name'] . ' Tutorial: Beginner to Advanced | TCM One';
        $description = 'A practical ' . $guide['name'] . ' learning guide from fundamentals to advanced topics, with explanations, examples, practice tasks and project ideas.';
        $canonical = $appUrl . '/learn/' . rawurlencode($slug);
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'LearningResource',
                    '@id' => $canonical . '#learning-resource',
                    'name' => $guide['name'] . ' Tutorial: Beginner to Advanced',
                    'description' => $description,
                    'url' => $canonical,
                    'learningResourceType' => 'Tutorial',
                    'educationalLevel' => $guide['level'],
                    'inLanguage' => 'en-IN',
                    'isAccessibleForFree' => true,
                    'provider' => ['@type' => 'Organization', 'name' => 'TCM One', 'url' => $appUrl],
                    'teaches' => array_column($guide['lessons'], 'title'),
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $appUrl . '/'],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Learn', 'item' => $appUrl . '/learn'],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $guide['name'], 'item' => $canonical],
                    ],
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => array_map(static fn (array $item): array => [
                        '@type' => 'Question',
                        'name' => $item['q'],
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
                    ], $guide['faq']),
                ],
            ],
        ];

        $this->view('public/learn/show', [
            'guide' => $guide,
            'slug' => $slug,
            'guides' => $guides,
            'appUrl' => $appUrl,
            'canonical' => $canonical,
            'title' => $title,
            'description' => $description,
            'ogImage' => $appUrl . '/assets/tcm-og-share.png',
            'schema' => $schema,
        ], null);
    }
}
