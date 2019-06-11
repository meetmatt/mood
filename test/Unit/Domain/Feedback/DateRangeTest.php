<?php declare(strict_types=1);

namespace Unit\Domain\Feedback;

use DateTime;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use MeetMatt\Colla\Mood\Domain\Feedback\DateRange;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \MeetMatt\Colla\Mood\Domain\Feedback\DateRange
 */
class DateRangeTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getStart
     * @covers ::getEnd
     */
    public function testGetters(): void
    {
        $start = new DateTime('2019-01-01');
        $end   = new DateTime('2019-01-02');

        $sut = new DateRange($start, $end);

        $this->assertSame($start, $sut->getStart());
        $this->assertSame($end, $sut->getEnd());
    }

    /**
     * @covers ::createFromString
     */
    public function testCreateFromStringWithSingleDate(): void
    {
        $sut = DateRange::createFromString('2019-01-01');

        $this->assertSame('2019-01-01', $sut->getStart()->format('Y-m-d'));
        $this->assertSame('2019-01-01', $sut->getEnd()->format('Y-m-d'));
    }

    /**
     * @covers ::createFromString
     */
    public function testCreateFromStringWithRangeFormat(): void
    {
        $sut = DateRange::createFromString('2019-01-01 to 2019-02-01');

        $this->assertSame('2019-01-01', $sut->getStart()->format('Y-m-d'));
        $this->assertSame('2019-02-01', $sut->getEnd()->format('Y-m-d'));
    }

    /**
     * @covers ::createFromString
     *
     * @dataProvider provideInvalidRangeFormat
     *
     * @param string $invalidRange
     * @param string $errorMessage
     */
    public function testCreateFromStringThrowsExceptionIfDateRangeIsInvalid(
        string $invalidRange,
        string $errorMessage
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errorMessage);

        DateRange::createFromString($invalidRange);
    }

    public function provideInvalidRangeFormat(): array
    {
        return [
            'Empty string'              => [
                '',
                'Missing date range.',
            ],
            'Invalid string'            => [
                'abc',
                'Invalid start date format.',
            ],
            'Invalid start date format' => [
                'abc to 2019-01-01',
                'Invalid start date format.',
            ],
            'Invalid end date format'   => [
                '2019-01-01 to abd',
                'Invalid end date format.',
            ],
        ];
    }

    /**
     * @covers ::createDefault
     *
     * @throws Exception
     */
    public function testCreateDefaultReturnsTwoWeeksRange(): void
    {
        $end = new DateTimeImmutable('2019-01-15');

        $sut = DateRange::createDefault($end);

        $this->assertSame('2019-01-01', $sut->getStart()->format('Y-m-d'));
        $this->assertSame('2019-01-15', $sut->getEnd()->format('Y-m-d'));
    }
}
