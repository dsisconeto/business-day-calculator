<?php

namespace Test;


use DateTime;
use DSisconeto\BusinessDayCalculator\DayOfWeek;
use DSisconeto\BusinessDayCalculator\BusinessDaysCalculator;
use DSisconeto\BusinessDayCalculator\BusinessDayPolicy;
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

    public function dataProviderForDayLaterWithAdditional()
    {

        return [
            [
                [DayOfWeek::SUNDAY, DayOfWeek::SATURDAY],
                $this->getHolidays(),
                ['2018-11-01', '2018-11-05', '2018-11-06', '2018-11-07', '2018-11-08', '2018-11-09', '2018-11-12',
                    '2018-11-13', '2018-11-14', '2018-11-16', '2018-11-19', '2018-11-20', '2018-11-21', '2018-11-22',
                    '2018-11-23', '2018-11-26', '2018-11-27', '2018-11-28', '2018-11-29',
                    '2018-11-30', '2018-12-03', '2018-12-04', '2018-12-05', '2018-12-06', '2018-12-07', '2018-12-10',
                    '2018-12-11', '2018-12-12', '2018-12-13', '2018-12-14', '2018-12-17',
                ],
                new DateTime('2018-11-01'),
                30
            ],

        ];
    }

    /**
     * @param $ignoreDaysOfWeek
     * @param $holidays
     * @param $expected
     * @param $startAt
     * @param $daysLater
     * @dataProvider dataProviderForDayLaterWithAdditional
     */
    public function test_from_day_later_with_additional_days($ignoreDaysOfWeek, $holidays, $expected, $startAt, $daysLater)
    {
        $this->businessDayPolicy->setIgnoreDaysOfWeek($ignoreDaysOfWeek)
            ->addHolidays($holidays);
        $datesDaysLater = $this->businessDayCalculator->fromDays(
            $startAt,
            $daysLater,
            true);

        $this->assertEquals($expected, $this->format($datesDaysLater));
    }


    public function test_next_business_day()
    {
        $this->businessDayPolicy->setIgnoreDaysOfWeek([DayOfWeek::SATURDAY, DayOfWeek::SUNDAY])
            ->addHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

        $nextBusinessDay = $this->businessDayCalculator->nextBusinessDay(new DateTime('2018-11-02'));

        $this->assertEquals($nextBusinessDay->format('Y-m-d'), (new DateTime('2018-11-05'))->format('Y-m-d'));
    }

    private function format($dates): array
    {
        return array_map(function (DateTime $date) {
            return $date->format('Y-m-d');
        }, $dates);
    }


}
