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
        $title = 'Free Coding Tutorials: Python, Java, Web & GitHub CI/CD | TCM One';
        $description = 'Learn Python, Java, HTML, CSS, SCSS, Bootstrap and GitHub Actions CI/CD step by step with clear explanations, practical examples and projects.';

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
        $seo = [
            'python' => [
                'title' => 'Python Tutorial for Beginners to Advanced | TCM One',
                'description' => 'Learn Python step by step with beginner-friendly lessons on variables, loops, functions, files, classes, testing and practical coding projects.',
            ],
            'html' => [
                'title' => 'HTML Tutorial for Beginners: Semantic HTML | TCM One',
                'description' => 'Learn HTML step by step: build a valid web page, use semantic elements, add accessible images and forms, and write useful page metadata.',
            ],
            'css' => [
                'title' => 'CSS Tutorial: Layout, Flexbox & Grid | TCM One',
                'description' => 'Learn CSS from the basics to responsive design. Understand selectors, the box model, Flexbox, Grid, media queries and accessible styling.',
            ],
            'scss' => [
                'title' => 'Sass SCSS Tutorial: Beginner to Advanced | TCM One',
                'description' => 'Learn Sass and SCSS with clear examples of variables, nesting, modules, mixins and functions, then compile and organize maintainable CSS.',
            ],
            'bootstrap' => [
                'title' => 'Bootstrap Tutorial: Responsive Web Design | TCM One',
                'description' => 'Learn Bootstrap step by step: use its responsive grid, utilities, components, forms and JavaScript plugins while keeping pages accessible.',
            ],
            'java' => [
                'title' => 'Java Tutorial for Beginners to Advanced | TCM One',
                'description' => 'Learn Java with simple explanations and code examples for variables, methods, classes, collections, exceptions, testing and concurrency.',
            ],
            'github-cicd' => [
                'title' => 'GitHub Actions CI/CD Tutorial: Build, Test & Deploy | TCM One',
                'description' => 'Learn GitHub Actions CI/CD from the basics: create workflow YAML, test pull requests, deploy a static site to GitHub Pages, and protect secrets and production environments.',
            ],
        ];
        $title = $seo[$slug]['title'];
        $description = $seo[$slug]['description'];
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
