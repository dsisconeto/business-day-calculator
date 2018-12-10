<?php

namespace Tests;

use DateTime;
use DSisconeto\BusinessDayCalculator\BusinessDayPolicy;
use DSisconeto\BusinessDayCalculator\DayOfWeek;
use DSisconeto\BusinessDayCalculator\HolidayIntervalInterface;
use DSisconeto\BusinessDayCalculator\HolidayUniqueInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BusinessDayPolicyTest
 * @package Tests
 */
class BusinessDayPolicyTest extends TestCase
{
    /**
     * @var BusinessDayPolicy
     */
    private $businessDayPolicy;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->businessDayPolicy = new BusinessDayPolicy();
    }

    /**
     * @throws \Exception
     */
    public function testIsNotBusinessDayOnWeekends(): void
    {
        $this->businessDayPolicy->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY]);
        $this->assertFalse($this->businessDayPolicy->isBusinessDay(new DateTime('2018-11-03')));
    }

    /**
     * @throws \Exception
     */
    public function testIsNotBusinessDayOnHolidays(): void
    {
        $this->businessDayPolicy
            ->setHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

        $this->assertFalse($this->businessDayPolicy->isBusinessDay(new DateTime('2018-11-02')));
        $this->assertFalse($this->businessDayPolicy->isBusinessDay(new DateTime('2018-11-15')));
    }

    /**
     * @throws \Exception
     */
    public function testIsNotBusinessDayOnHolidaysAndWeekends(): void
    {
        $this->businessDayPolicy
            ->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY])
            ->setHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

        $this->assertFalse($this->businessDayPolicy->isBusinessDay(new DateTime('2018-11-02')));
        $this->assertFalse($this->businessDayPolicy->isBusinessDay(new DateTime('2018-11-15')));

        $this->assertFalse($this->businessDayPolicy->isBusinessDay(new DateTime('2018-11-03')));
        $this->assertFalse($this->businessDayPolicy->isBusinessDay(new DateTime('2018-11-04')));
    }

    /**
     * @throws \Exception
     */
    public function testIsBusinessDayWithWeekendsAndHolidays(): void
    {
        $this->businessDayPolicy
            ->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY])
            ->setHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

        $this->assertTrue($this->businessDayPolicy->isBusinessDay(new DateTime('2018-11-14')));
        $this->assertTrue($this->businessDayPolicy->isBusinessDay(new DateTime('2018-11-29')));
    }


    /**
     * @throws \Exception
     */
    public function testDateIntervalAndDateUnique()
    {

        $holidayInterval = $this->factoryHolidayInterval('2018-06-01', '2018-06-10');
        $holidayUnique = $this->factoryHolidayUnique('2018-12-25');

        $holidays = [
            $holidayInterval,
            $holidayUnique,
            new DateTime('2018-09-07'),
        ];

        $this->businessDayPolicy->setHolidays($holidays);

        $expectedIsNotBusinessDay = [
            '2018-06-01',
            '2018-06-02',
            '2018-06-03',
            '2018-06-04',
            '2018-06-05',
            '2018-06-06',
            '2018-06-07',
            '2018-06-08',
            '2018-06-09',
            '2018-06-10',
            '2018-09-07',
            '2018-12-25'
        ];

        $expectedIsBusinessDay = [
            '2018-05-31',
            '2018-06-11',
            '2018-12-26'
        ];

        foreach ($expectedIsNotBusinessDay as $holiday) {
            $isBusinessDay = $this->businessDayPolicy->isBusinessDay(new DateTime($holiday));
            $this->assertFalse($isBusinessDay, "$holiday deveria ser um feriado");
        }


        foreach ($expectedIsBusinessDay as $holiday) {
            $isBusinessDay = $this->businessDayPolicy->isBusinessDay(new DateTime($holiday));
            $this->assertTrue($isBusinessDay, "$holiday não deveria ser um feriado");
        }


    }

    /**
     * @param string $startAt
     * @param string $endAt
     * @return HolidayIntervalInterface
     * @throws \Exception
     */
    private function factoryHolidayInterval(string $startAt, string $endAt): HolidayIntervalInterface
    {
        $holidayInterval = new class implements HolidayIntervalInterface
        {
            private $start;
            private $end;

            public function getStart(): \DateTime
            {
                return $this->start;
            }

            public function getEnd(): \DateTime
            {
                return $this->end;
            }

            public function setStart($start): void
            {
                $this->start = $start;
            }

            public function setEnd($end): void
            {
                $this->end = $end;
            }
        };

        $holidayInterval->setStart(new DateTime($startAt));
        $holidayInterval->setEnd(new DateTime($endAt));

        return $holidayInterval;
    }

    /**
     * @param string $date
     * @return HolidayUniqueInterface
     * @throws \Exception
     */
    private function factoryHolidayUnique(string $date): HolidayUniqueInterface
    {
        $holidayUnique = new class implements HolidayUniqueInterface
        {
            private $date;

            /**
             * @param mixed $date
             */
            public function setDate($date): void
            {
                $this->date = $date;
            }

            public function getDate(): DateTime
            {
                return $this->date;
            }
        };

        $holidayUnique->setDate(new DateTime($date));

        return $holidayUnique;
    }


}
