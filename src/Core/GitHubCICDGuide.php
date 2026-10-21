<?php

declare(strict_types=1);

namespace TCM\Core;

/** Beginner-friendly GitHub Actions CI/CD guide, kept separate from language curriculum. */
final class GitHubCICDGuide
{
    /** @return array<string,array<string,mixed>> */
    public static function all(): array
    {
        return [
            'github-cicd' => [
                'name' => 'GitHub CI/CD',
                'category' => 'DevOps and automation',
                'version' => 'GitHub Actions · checked 21 October 2026',
                'versionNote' => 'GitHub Actions changes over time. The workflow example follows current official syntax and uses GitHub Pages as a concrete static-site deployment; check the official references before updating action versions.',
                'level' => 'Beginner to advanced',
                'duration' => '10 modules · learn at your own pace',
                'icon' => 'bi-github',
                'summary' => 'Learn how GitHub Actions checks every code change, runs tests and publishes an approved website, with simple examples and a safe release flow.',
                'prerequisites' => 'A GitHub account and a repository are enough to follow along. The working sample uses a small Node.js static site; other languages use the same flow with their own setup and test commands.',
                'outcome' => 'Build a pull-request check, deploy passing static files to GitHub Pages, and explain how to protect production credentials and releases.',
                'courseCategory' => 'Cloud & DevOps',
                'lessons' => [
                    [
                        'title' => '1. CI/CD in one simple flow',
                        'explanation' => 'Imagine you prepare an assignment before submitting it. You check spelling and make sure it opens: that is Continuous Integration (CI), automatic checks that run when code changes. Continuous Delivery means every passing change is ready to publish, but a person can approve the final release. Continuous Deployment goes one step further and publishes each passing change automatically. The everyday flow is: create a branch → change code → open a pull request → GitHub runs checks → review and merge → deploy the approved version. A failed check stops the flow before broken code is published.',
                        'example' => "feature branch → pull request → CI checks\n                         ↓ checks pass + review\nmain branch → build → deploy to website\n\nCI = automatically check code changes\nCD = automatically prepare or publish approved changes",
                        'practice' => 'Draw this flow for a change to a personal website. Mark where tests run, where a person reviews the change and when website visitors see it.',
                    ],
                    [
                        'title' => '2. Understand workflows, events, jobs and runners',
                        'explanation' => 'A workflow is an automation recipe stored in YAML under .github/workflows. An event starts the recipe; common events are a pull request, a push to main or a manual run. A job is a group of work that runs on a runner, which is a temporary computer provided by GitHub. A job has steps: a step can run a command or use a reusable action. A build artifact is a file produced by the workflow, such as the finished website folder.',
                        'example' => "repository/\n├── .github/\n│   └── workflows/\n│       └── site.yml\n├── package.json\n├── package-lock.json\n└── public/",
                        'practice' => 'Create the .github/workflows folder in a practice repository and add an empty site.yml file.',
                    ],
                    [
                        'title' => '3. Prepare a small site and repeatable commands',
                        'explanation' => 'A workflow can only repeat tasks your project already knows how to do. This example expects package.json to have test and build scripts, and a package-lock.json that matches it. npm ci installs the saved package versions, npm test runs checks, and npm run build creates deployable files in dist. Run these commands on your own computer first. A PHP or Python app keeps the same workflow shape but replaces Node setup and npm commands with its own install and test commands.',
                        'example' => <<<'CODE'
{
  "scripts": {
    "test": "node --test",
    "build": "mkdir -p dist && cp -r public/. dist/"
  }
}
CODE,
                        'practice' => 'Run npm ci, npm test and npm run build locally. Confirm that dist contains your website before adding automation.',
                    ],
                    [
                        'title' => '4. Create your first CI workflow',
                        'explanation' => 'Create .github/workflows/site.yml. This workflow runs for pull requests into main and for pushes to main. The build job checks out the files, installs Node.js 24 and locked dependencies, runs tests, builds the site and packages dist as a Pages artifact. GitHub displays every step and its output in the Actions tab. The workflow has read-only access to repository content; keep permissions this small unless a later job needs more.',
                        'example' => <<<'CODE'
name: Site CI and deployment

on:
  pull_request:
    branches: [main]
  push:
    branches: [main]

permissions:
  contents: read

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - name: Get repository files
        uses: actions/checkout@v7
      - name: Install Node.js
        uses: actions/setup-node@v7
        with:
          node-version: 24
      - name: Install exact dependencies
        run: npm ci
      - name: Run tests
        run: npm test
      - name: Build the website
        run: npm run build
      - name: Package website for Pages
        uses: actions/upload-pages-artifact@v4
        with:
          path: dist
CODE,
                        'practice' => 'Commit the workflow on a branch and open a pull request. Open the Actions tab, find its run and fix any failed step before merging.',
                    ],
                    [
                        'title' => '5. Use pull requests as the safety gate',
                        'explanation' => 'A pull request is a review area for a proposed change before it becomes part of main. The workflow checks the proposed code, so reviewers can see whether tests and the build pass. In repository Settings → Branches, protect main by requiring a pull request and successful status checks before merging. This catches problems before they reach the production branch. Begin with one required check; add more rules when your team has a clear reason.',
                        'example' => "1. Create a feature branch\n2. Edit files and push that branch\n3. Open a pull request into main\n4. Wait for the Actions check\n5. Review the changes and fix failed checks\n6. Merge only after required checks pass",
                        'practice' => 'Open a pull request that changes one line in your practice site. Confirm that the CI run is attached to the pull request and passes.',
                    ],
                    [
                        'title' => '6. Add CD and publish the site with GitHub Pages',
                        'explanation' => 'The CI job made a tested website artifact; a separate deploy job now publishes it. It waits for build to finish and only runs after a push to main, so an unmerged pull request cannot publish the site. In the repository, open Settings → Pages and choose GitHub Actions as the publishing source. After a passing change is merged, open Actions to see deployment status and the published page URL. GitHub Pages serves static HTML, CSS and JavaScript files; it does not run a PHP server or database.',
                        'example' => <<<'CODE'
  deploy:
    if: github.event_name == 'push' && github.ref == 'refs/heads/main'
    needs: build
    runs-on: ubuntu-latest
    permissions:
      contents: read
      pages: write
      id-token: write
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}
    steps:
      - name: Publish the built site
        id: deployment
        uses: actions/deploy-pages@v4
CODE,
                        'practice' => 'Enable GitHub Actions as the Pages source, merge a passing change to main and follow the deploy job to its page URL.',
                    ],
                    [
                        'title' => '7. Keep passwords and deployment secrets safe',
                        'explanation' => 'A secret is private data such as a hosting token or SSH key. Never paste it into source code or a workflow file because those files and logs can expose it. Add secrets in Settings → Secrets and variables → Actions, then read them with the secrets context only in the deployment step that needs them. Put production credentials in a production environment and add branch rules or required approval if the release needs a person to authorize it. Workflows from forked pull requests do not receive normal repository secrets. The GitHub Pages example needs no custom password, so it does not use a secret.',
                        'example' => <<<'CODE'
- name: Deploy to your hosting provider
  env:
    HOSTING_TOKEN: ${{ secrets.HOSTING_TOKEN }}
  run: ./scripts/deploy.sh
CODE,
                        'practice' => 'List what credentials your chosen host needs. For each credential, note the one trusted deploy step that uses it and who should be allowed to approve production releases.',
                    ],
                    [
                        'title' => '8. Read a failed run and repair the pipeline',
                        'explanation' => 'A red workflow does not always mean GitHub is broken. Open the run, choose the failed job and read its step log from the first error upward. npm ci often fails if the lockfile is missing or does not match package.json. A failed test means the test command ran but found behavior it did not expect. A missing dist folder means the build output and artifact path do not match. A skipped deploy on a pull request is expected: publishing is limited to main. Make one fix, push again and let the same workflow check it.',
                        'example' => "npm ci fails       → update and commit package-lock.json\nnpm test fails     → inspect the failing assertion\nArtifact upload fails → check that build creates dist/\nDeploy is skipped  → expected for a pull request",
                        'practice' => 'Temporarily use a command that fails, find its message in the Actions run, then restore the correct command and push the repair.',
                    ],
                    [
                        'title' => '9. Harden production releases',
                        'explanation' => 'Give each job only the permissions it needs. Run tests for pull requests, but expose production credentials only to a protected deployment environment on a trusted branch. A concurrency group can prevent two production deploys from racing. Save reports or build files as artifacts when you need to inspect or reuse them. For stronger supply-chain security, pin third-party actions to reviewed full commit SHAs and update them deliberately; major-version tags are easier to read, but the tag can move. Keep dependencies and action versions maintained.',
                        'example' => <<<'CODE'
concurrency:
  group: pages-production
  cancel-in-progress: true

# Production release pattern:
# - test pull requests without production secrets
# - deploy only a protected main branch
# - require approval for a production environment
CODE,
                        'practice' => 'Review your workflow permissions and remove any permission that no step uses. Identify which job is allowed to publish production.',
                    ],
                    [
                        'title' => '10. Adapt the pipeline to PHP, Python or another app',
                        'explanation' => 'The repeatable pattern is install → lint → test → build → deploy. A PHP application can run syntax checks and its test suite before a separate SSH or hosting-provider deploy job. A Python application can install pinned requirements and run pytest. GitHub Pages is only for static files; a PHP app needs a host that runs PHP and any database it uses. Keep the pull-request checks and replace only the language setup and destination-specific deploy steps. Use the current deployment guide from your chosen host, and never copy a Pages step to a backend server expecting it to run PHP.',
                        'example' => "Every project: install → lint → test → build → deploy\nStatic site: publish HTML, CSS and JavaScript files\nPHP or database app: choose a PHP-capable host\n\nKeep pull-request checks when the deploy destination changes.",
                        'practice' => 'Write a release plan for your own project: install command, tests, build output folder, hosting target and the first step you would take to roll back a bad release.',
                    ],
                ],
                'faq' => [
                    ['q' => 'Does a green CI run guarantee a bug-free site?', 'a' => 'No. It means the checks you configured passed. Include meaningful tests, reviews and production safeguards for the risks in your project.'],
                    ['q' => 'Can GitHub Pages run PHP or a database?', 'a' => 'No. Pages publishes static files. A PHP application needs hosting that runs PHP and its database; the CI checks can stay and the deploy job must change.'],
                    ['q' => 'Should I put a deploy password in my YAML file?', 'a' => 'No. Store credentials as GitHub Actions secrets or protected environment secrets, and expose them only to the trusted deployment job that needs them.'],
                ],
            ],
        ];
    }
}
