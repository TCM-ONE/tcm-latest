<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Models\Course;
use TCM\Services\DiwaliOffer;

final class DiwaliOfferController extends Controller
{
    public function show(): void
    {
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $canonical = $appUrl . '/diwali-offer-2026';
        $active = DiwaliOffer::isActive();
        $courses = [];

        if ($active) {
            foreach (Course::listWithCategory(['status' => 'published']) as $course) {
                $regularPrice = (float) ($course['price'] ?? 0);
                if (!DiwaliOffer::includes((string) ($course['slug'] ?? ''), $regularPrice)) {
                    continue;
                }

                $course['regular_price'] = $regularPrice;
                $course['offer_price'] = DiwaliOffer::priceFor((string) $course['slug'], $regularPrice);
                $courses[] = $course;
            }
        }

        $title = $active
            ? 'Diwali 2026 Course Offer: Up to 60% Off | TCM One'
            : 'Diwali 2026 Course Offer Has Ended | TCM One';
        $description = $active
            ? 'Explore TCM One’s Diwali 2026 coding course offer: save 60% on eligible courses above ₹8,000, plus selected special prices. Full payment only; ends 10 November.'
            : 'TCM One’s Diwali 2026 course offer has ended. Browse current Full Stack, Python, AI, data analytics and DSA course fees and schedules.';

        $this->view('public/offers/diwali-2026', [
            'active' => $active,
            'courses' => $courses,
            'appUrl' => $appUrl,
            'canonical' => $canonical,
            'title' => $title,
            'description' => $description,
            'endsAt' => DiwaliOffer::endsAtIso(),
        ], null);
    }
}
