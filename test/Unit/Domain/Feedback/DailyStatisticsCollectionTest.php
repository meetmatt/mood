<?php declare(strict_types=1);

namespace Unit\Domain\Feedback;

use DateTime;
use Exception;
use MeetMatt\Colla\Mood\Domain\Feedback\DailyStatistics;
use MeetMatt\Colla\Mood\Domain\Feedback\DailyStatisticsCollection;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \MeetMatt\Colla\Mood\Domain\Feedback\DailyStatisticsCollection
 */
class DailyStatisticsCollectionTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::add
     *
     * @throws Exception
     */
    public function testAdd(): void
    {
        $firstDailyStatistics  = $this->createDailyStatistics('2019-01-01');
        $secondDailyStatistics = $this->createDailyStatistics('2019-02-02');

        $sut = new DailyStatisticsCollection([$firstDailyStatistics]);
        $sut->add($secondDailyStatistics);

        $this->assertSame([$firstDailyStatistics, $secondDailyStatistics], $sut->all());
    }

    /**
     * @covers ::add
     *
     * @throws Exception
     */
    public function testAddWontDuplicateItems(): void
    {
        $dailyStatistics = $this->createDailyStatistics();

        $sut = new DailyStatisticsCollection();
        $sut->add($dailyStatistics);
        $sut->add($dailyStatistics);

        $this->assertSame([$dailyStatistics], $sut->all());
    }

    /**
     * @covers ::__construct
     * @covers ::all
     *
     * @throws Exception
     */
    public function testAll(): void
    {
        $sut = new DailyStatisticsCollection();

        $this->assertSame([], $sut->all());

        $dailyStatistics = $this->createDailyStatistics();

        $sut->add($dailyStatistics);

        $this->assertSame([$dailyStatistics], $sut->all());
    }

    /**
     * @covers ::getWholeAverage
     *
     * @dataProvider provideDailyAveragesForOveralCalculation
     *
     * @param array $dailyAverages
     * @param float $wholeAverage
     *
     * @throws Exception
     */
    public function testGetWholeAverage(array $dailyAverages, float $wholeAverage): void
    {
        $sut = new DailyStatisticsCollection();

        foreach ($dailyAverages as $date => $dailyAverage) {
            $sut->add($this->createDailyStatistics($date, $dailyAverage));
        }

        $this->assertSame($wholeAverage, $sut->getWholeAverage());
    }

    public function provideDailyAveragesForOveralCalculation(): array
    {
        return [
            [
                [
                    '2019-01-01' => 1.0
                ],
                1.0
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 1.0,
                ],
                1.0
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 2.0,
                ],
                1.5
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 2.0,
                    '2019-01-03' => 1.0,
                ],
                1.3
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 2.0,
                    '2019-01-03' => 3.0,
                    '2019-01-04' => 4.0,
                    '2019-01-05' => 5.0,
                ],
                3.0
            ],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::getWholeAverage
     */
    public function testGetWholeAverageReturnsZeroIfStatisticsAreEmpty(): void
    {
        $sut = new DailyStatisticsCollection();

        $this->assertSame(0.0, $sut->getWholeAverage());
    }

    /**
     * @covers ::getBestDay
     *
     * @dataProvider provideDailyAveragesForBestDayCalculation
     *
     * @param array  $dailyAverages
     * @param float  $bestAverage
     * @param string $bestDate
     *
     * @throws Exception
     */
    public function testGetBestDay(array $dailyAverages, float $bestAverage, string $bestDate): void
    {
        $sut = new DailyStatisticsCollection();

        foreach ($dailyAverages as $date => $dailyAverage) {
            $sut->add($this->createDailyStatistics($date, $dailyAverage));
        }

        $bestDay = $sut->getBestDay();

        $this->assertSame($bestAverage, $bestDay->getAverageRating());
        $this->assertSame($bestDate, $bestDay->getDate()->format('Y-m-d'));
    }

    public function provideDailyAveragesForBestDayCalculation(): array
    {
        return [
            [
                [
                    '2019-01-01' => 1.0
                ],
                1.0,
                '2019-01-01',
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 1.0,
                ],
                1.0,
                '2019-01-02',
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 2.0,
                ],
                2.0,
                '2019-01-02',
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 2.0,
                    '2019-01-03' => 1.0,
                ],
                2,
                '2019-01-02',
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 2.0,
                    '2019-01-03' => 3.0,
                    '2019-01-04' => 4.0,
                    '2019-01-05' => 5.0,
                ],
                5.0,
                '2019-01-05',
            ],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::getBestDay
     */
    public function testGetBestDayReturnsNullIfStatisticsAreEmpty(): void
    {
        $sut = new DailyStatisticsCollection();

        $this->assertNull($sut->getBestDay());
    }

    /**
     * @covers ::getBestDay
     *
     * @dataProvider provideDailyAveragesForWorstDayCalculation
     *
     * @param array  $dailyAverages
     * @param float  $worstAverage
     * @param string $worstDate
     *
     * @throws Exception
     */
    public function testGetWorstDay(array $dailyAverages, float $worstAverage, string $worstDate): void
    {
        $sut = new DailyStatisticsCollection();

        foreach ($dailyAverages as $date => $dailyAverage) {
            $sut->add($this->createDailyStatistics($date, $dailyAverage));
        }

        $worstDay = $sut->getWorstDay();

        $this->assertSame($worstAverage, $worstDay->getAverageRating());
        $this->assertSame($worstDate, $worstDay->getDate()->format('Y-m-d'));
    }

    public function provideDailyAveragesForWorstDayCalculation(): array
    {
        return [
            [
                [
                    '2019-01-01' => 1.0
                ],
                1.0,
                '2019-01-01',
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 1.0,
                ],
                1.0,
                '2019-01-02',
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 2.0,
                ],
                1.0,
                '2019-01-01',
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 2.0,
                    '2019-01-03' => 1.0,
                ],
                1.0,
                '2019-01-03',
            ],
            [
                [
                    '2019-01-01' => 2.0,
                    '2019-01-02' => 1.0,
                    '2019-01-03' => 3.0,
                ],
                1.0,
                '2019-01-02',
            ],
            [
                [
                    '2019-01-01' => 1.0,
                    '2019-01-02' => 2.0,
                    '2019-01-03' => 3.0,
                    '2019-01-04' => 4.0,
                    '2019-01-05' => 5.0,
                ],
                1.0,
                '2019-01-01',
            ],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::getWorstDay
     */
    public function testGetWorstDayReturnsNullIfStatisticsAreEmpty(): void
    {
        $sut = new DailyStatisticsCollection();

        $this->assertNull($sut->getWorstDay());
    }

    /**
     * @param string $date
     * @param float  $average
     *
     * @return DailyStatistics
     *
     * @throws Exception
     */
    private function createDailyStatistics(string $date = '2019-01-01', float $average = 4.5): DailyStatistics
    {
        return new DailyStatistics(new DateTime($date), $average);
    }
}
