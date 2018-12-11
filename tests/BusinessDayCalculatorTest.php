<?php

namespace Tests;

use DateTime;
use DSisconeto\BusinessDayCalculator\BusinessDayPolicy;
use DSisconeto\BusinessDayCalculator\BusinessDaysCalculator;
use DSisconeto\BusinessDayCalculator\DayOfWeek;
use PHPUnit\Framework\TestCase;

/**
 * Class BusinessDayCalculatorTest
 * @package Test
 */
class BusinessDayCalculatorTest extends TestCase
{

    /**
     * @var BusinessDayPolicy
     */
    private $businessDayPolicy;
    /**
     * @var BusinessDaysCalculator
     */
    private $businessDayCalculator;

    public function setUp()
    {
        $this->businessDayPolicy = new BusinessDayPolicy();
        $this->businessDayCalculator = new BusinessDaysCalculator($this->businessDayPolicy);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function dataProviderForDayLaterWithExtendable(): array
    {

        return [
            [
                [DayOfWeek::SUNDAY, DayOfWeek::SATURDAY],
                $this->getHolidays(),
                [
                    '2018-11-01',
                    '2018-11-05',
                    '2018-11-06',
                    '2018-11-07',
                    '2018-11-08',
                    '2018-11-09',
                    '2018-11-12',
                    '2018-11-13',
                    '2018-11-14',
                    '2018-11-16',
                    '2018-11-19',
                    '2018-11-20',
                    '2018-11-21',
                    '2018-11-22',
                    '2018-11-23',
                    '2018-11-26',
                    '2018-11-27',
                    '2018-11-28',
                    '2018-11-29',
                    '2018-11-30',
                    '2018-12-03',
                    '2018-12-04',
                    '2018-12-05',
                    '2018-12-06',
                    '2018-12-07',
                    '2018-12-10',
                    '2018-12-11',
                    '2018-12-12',
                    '2018-12-13',
                    '2018-12-14',
                    '2018-12-17',
                ],
                new DateTime('2018-11-01'),
                30,
                true
            ],

        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function dataProviderForDateInterval(): array
    {

        return [
            [
                [DayOfWeek::SUNDAY, DayOfWeek::SATURDAY],
                array_merge([new DateTime('2018-12-14')], $this->getHolidays()),
                [
                    '2018-12-17',
                    '2018-12-18',
                    '2018-12-19',
                    '2018-12-20',
                    '2018-12-21',
                    '2018-12-24',
                    '2018-12-26',
                    '2018-12-27',
                    '2018-12-28',
                ],
                new DateTime('2018-12-14'),
                new DateTime('2018-12-28'),
                false,
            ],
        ];
    }


    /**
     * @param $ignoreDaysOfWeek
     * @param $holidays
     * @param $businessDaysExpected
     * @param $startAt
     * @param $daysLater
     * @param $extendable
     * @dataProvider dataProviderForDayLaterWithExtendable
     */
    public function testFromDayLaterWithDays(
        $ignoreDaysOfWeek,
        $holidays,
        $businessDaysExpected,
        $startAt,
        $daysLater,
        $extendable
    ): void {
        $this->businessDayPolicy->setIgnoreDaysOfWeek($ignoreDaysOfWeek)
            ->setHolidays($holidays);
        $businessDays = $this->businessDayCalculator->fromDays($startAt, $daysLater, $extendable);
        $this->assertEquals($businessDaysExpected, $this->format($businessDays->getDates()));
        $this->assertEquals(count($businessDaysExpected), $businessDays->count());
        $this->assertEquals(
            $businessDaysExpected[0],
            $businessDays->getDateStart()->format('Y-m-d')
        );
        $this->assertEquals(
            $businessDaysExpected[count($businessDaysExpected) - 1],
            $businessDays->getDateEnd()->format('Y-m-d')
        );
    }

    /**
     * @param $ignoreDaysOfWeek
     * @param $holidays
     * @param $businessDaysExpected
     * @param $startAt
     * @param $endAt
     * @param $extendable
     * @dataProvider dataProviderForDateInterval
     */
    public function testFromDateInterval(
        $ignoreDaysOfWeek,
        $holidays,
        $businessDaysExpected,
        $startAt,
        $endAt,
        $extendable
    ): void {
        $this->businessDayPolicy->setIgnoreDaysOfWeek($ignoreDaysOfWeek)
            ->setHolidays($holidays);

        $businessDays = $this->businessDayCalculator->fromInterval($startAt, $endAt, $extendable);

        $this->assertEquals($businessDaysExpected, $this->format($businessDays->getDates()));
        $this->assertEquals(count($businessDaysExpected), $businessDays->count());
        $this->assertEquals(
            $businessDaysExpected[0],
            $businessDays->getDateStart()->format('Y-m-d')
        );
        $this->assertEquals(
            $businessDaysExpected[count($businessDaysExpected) - 1],
            $businessDays->getDateEnd()->format('Y-m-d')
        );
    }

    public function testNextBusinessDay(): void
    {
        $this->businessDayPolicy
            ->setHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')])
            ->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY]);

        $nextBusinessDay = $this->businessDayCalculator->nextBusinessDay(new DateTime('2018-11-02'));


        $expected = (new DateTime('2018-11-05'))->format('Y-m-d');
        $this->assertEquals($expected, $nextBusinessDay->format('Y-m-d'));
    }


    private function format($dates): array
    {
        return array_map(function (DateTime $date) {
            return $date->format('Y-m-d');
        }, $dates);
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getHolidays(): array
    {
        /// https://www.calendarr.com/brasil/calendario-2018/
        return [
            new DateTime('2018-01-01'),
            new DateTime('2018-03-30'),
            new DateTime('2018-04-01'),
            new DateTime('2018-04-21'),
            new DateTime('2018-05-01'),
            new DateTime('2018-05-31'),
            new DateTime('2018-09-07'),
            new DateTime('2018-10-12'),
            new DateTime('2018-11-02'),
            new DateTime('2018-11-15'),
            new DateTime('2018-12-25')
        ];
    }
}
