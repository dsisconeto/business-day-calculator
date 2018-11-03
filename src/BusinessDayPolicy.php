<?php

namespace DSisconeto\BusinessDayCalculator;


use DateTime;


/**
 * Class BusinessDayPolicy
 * @package DSisconeto\BusinessDayCalculator
 */
class BusinessDayPolicy implements BusinessDayPolicyInterface
{
    /**
     * @var DateTime[]
     */
    private $holidays = [];
    /**
     * @var DayOfWeek[]
     */
    private $ignoreDaysOfWeek = [];
    /**
     * @var string
     */
    private $dateFormat = 'Y-m-d';
    /**
     * @var BusinessDayPolicy
     */
    private $businessDayPolicyAdditional = null;


    /**
     * @param DateTime $day
     * @return bool
     */
    public function isBusinessDay(DateTime $day): bool
    {
        if (is_null($this->businessDayPolicyAdditional)) {
            return !$this->isIgnoreDayOfWeek($day) && !$this->isHoliday($day);
        }
        return !$this->isIgnoreDayOfWeek($day)
            && !$this->isHoliday($day)
            && $this->businessDayPolicyAdditional->isBusinessDay($day);
    }

    /**
     * @param DateTime $day
     * @return bool
     */
    private function isIgnoreDayOfWeek(DateTime $day): bool
    {
        return array_key_exists($day->format('w'), $this->ignoreDaysOfWeek);
    }

    /**
     * @param DateTime $holiday
     * @return bool
     */
    private function isHoliday(DateTime $holiday): bool
    {
        return array_key_exists($holiday->format($this->dateFormat), $this->holidays);
    }

    /**
     * @param DateTime[] $holidays
     * @return BusinessDayPolicy
     */
    public function addHolidays(array $holidays): BusinessDayPolicy
    {
        foreach ($holidays as $holiday) {
            $this->addHoliday($holiday);
        }
        return $this;
    }

    /**
     * @param DateTime $holiday
     */
    public function addHoliday(DateTime $holiday)
    {
        $this->holidays[$holiday->format($this->dateFormat)] = $holiday;
    }

    /**
     * @param DayOfWeek[] $ignoreDaysOfWeek
     * @return BusinessDayPolicy
     */
    public function setIgnoreDaysOfWeek($ignoreDaysOfWeek): BusinessDayPolicy
    {
        $this->ignoreDaysOfWeek = array_flip($ignoreDaysOfWeek);
        return $this;
    }


    /**
     * @return string
     */
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     * @return BusinessDayPolicy
     */
    public function setDateFormat(string $dateFormat): BusinessDayPolicy
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * @param BusinessDayPolicy $businessDayPolicyAdditional
     * @return BusinessDayPolicy
     */
    public function setBusinessDayPolicyAdditional(BusinessDayPolicy $businessDayPolicyAdditional): BusinessDayPolicy
    {
        $this->businessDayPolicyAdditional = $businessDayPolicyAdditional;
        return $this;
    }


}
