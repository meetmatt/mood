<?php declare(strict_types=1);

namespace MeetMatt\Colla\Mood\Domain\Feedback;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

final class DateRange
{
    /** @var DateTimeInterface */
    private $start;

    /** @var DateTimeInterface */
    private $end;

    public static function createFromString(string $dateRange): self
    {
        if (empty($dateRange)) {
            throw new InvalidArgumentException('Missing date range.');
        }

        $ranges = explode(' to ', $dateRange);

        $start = $ranges[0];
        $end   = $ranges[1] ?? $start;

        $startDate = DateTimeImmutable::createFromFormat('Y-m-d', $start);
        if ($startDate === false) {
            throw new InvalidArgumentException('Invalid start date format.');
        }

        $endDate = DateTimeImmutable::createFromFormat('Y-m-d', $end);
        if ($endDate === false) {
            throw new InvalidArgumentException('Invalid end date format.');
        }

        return new self($startDate, $endDate);
    }

    public static function createDefault(DateTimeImmutable $end): DateRange
    {
        $twoWeeksBefore = $end->sub(new DateInterval('P14D'));

        return new self($twoWeeksBefore, $end);
    }

    public function __construct(DateTimeInterface $start, DateTimeInterface $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function getStart(): DateTimeInterface
    {
        return $this->start;
    }

    public function getEnd(): DateTimeInterface
    {
        return $this->end;
    }
}
