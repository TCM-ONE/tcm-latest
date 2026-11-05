<?php

declare(strict_types=1);

namespace TCM\Services;

/** Fixed booking-deposit rules for the November 2026 project-course cohort. */
final class CourseSeatBooking
{
    public const AMOUNT = 1000.00;
    public const CLASS_STARTS_AT = '2026-11-12 00:00:00';
    public const TIMEZONE = 'Asia/Kolkata';

    private const COURSE_SLUGS = [
        'full-stack-project-lab-react-node',
        'frontend-portfolio-builder-html-css-js',
        'data-analysis-dashboard-sprint-excel-powerbi',
    ];

    public static function isAvailable(string $slug, ?\DateTimeImmutable $now = null): bool
    {
        if (!in_array($slug, self::COURSE_SLUGS, true)) {
            return false;
        }

        $zone = new \DateTimeZone(self::TIMEZONE);
        $now ??= new \DateTimeImmutable('now', $zone);
        return $now < new \DateTimeImmutable(self::CLASS_STARTS_AT, $zone);
    }

    /** @return list<array{amount:float,due_date:string,notes:string}> */
    public static function schedule(float $courseFee, ?\DateTimeImmutable $now = null): array
    {
        if ($courseFee <= self::AMOUNT) {
            throw new \InvalidArgumentException('The course fee must exceed the seat booking amount.');
        }

        $zone = new \DateTimeZone(self::TIMEZONE);
        $now ??= new \DateTimeImmutable('now', $zone);
        $start = new \DateTimeImmutable(self::CLASS_STARTS_AT, $zone);
        if ($now >= $start) {
            throw new \InvalidArgumentException('Seat booking has closed because the cohort has started.');
        }

        return [
            [
                'amount' => self::AMOUNT,
                'due_date' => $now->setTimezone($zone)->format('Y-m-d'),
                'notes' => 'Seat booking deposit. Balance is due before the class starts on 12 November 2026.',
            ],
            [
                'amount' => round($courseFee - self::AMOUNT, 2),
                'due_date' => $start->format('Y-m-d'),
                'notes' => 'Remaining course fee due before the class starts on 12 November 2026.',
            ],
        ];
    }
}
