<?php

namespace Tests;

use DateTime;
use DSisconeto\BusinessDayCalculator\DayOfWeek;
use DSisconeto\BusinessDayCalculator\BusinessDayPolicy;
use PHPUnit\Framework\TestCase;

class BusinessDayPolicyTest extends TestCase
{
    /**
     * @var BusinessDayPolicy
     */
    private $specification;

    public function setUp()
    {
        parent::setUp();
        $this->specification = new BusinessDayPolicy();
    }

    public function testIsNotBusinessDayOnWeekends()
    {
        $this->specification->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY]);
        $this->assertFalse($this->specification->isBusinessDay(new DateTime('2018-11-03')));
    }

    public function testIsNotBusinessDayOnHolidays()
    {
        $this->specification
            ->addHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

        $this->assertFalse($this->specification->isBusinessDay(new DateTime('2018-11-02')));
        $this->assertFalse($this->specification->isBusinessDay(new DateTime('2018-11-15')));
    }

    public function testIsNotBusinessDayOnHolidaysAndWeekends()
    {
        $this->specification
            ->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY])
            ->addHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

        $this->assertFalse($this->specification->isBusinessDay(new DateTime('2018-11-02')));
        $this->assertFalse($this->specification->isBusinessDay(new DateTime('2018-11-15')));

        $this->assertFalse($this->specification->isBusinessDay(new DateTime('2018-11-03')));
        $this->assertFalse($this->specification->isBusinessDay(new DateTime('2018-11-04')));
    }

    public function testIsBusinessDayWithWeekendsAndHolidays()
    {
        $this->specification
            ->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY])
            ->addHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

        $this->assertTrue($this->specification->isBusinessDay(new DateTime('2018-11-14')));
        $this->assertTrue($this->specification->isBusinessDay(new DateTime('2018-11-29')));
    }
}
