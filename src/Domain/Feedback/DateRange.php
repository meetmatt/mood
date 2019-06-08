<?php

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
        [$start, $end] = explode(' to ', $dateRange);

        if (empty($end)) {
            $end = $start;
        }

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

    public static function createDefault(): DateRange
    {
        $now            = new DateTimeImmutable();
        $twoWeeksBefore = $now->sub(new DateInterval('P14D'));

        return new self($twoWeeksBefore, $now);
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
