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
     * @var AdditionalPolicyInterface
     */
    private $AdditionalPolicy = null;


    /**
     * @param DateTime $day
     * @return bool
     */
    public function isBusinessDay(DateTime $day): bool
    {
        if (is_null($this->AdditionalPolicy)) {
            return !$this->isIgnoreDayOfWeek($day) && !$this->isHoliday($day);
        }
        return $this->AdditionalPolicy->isBusinessDay(
            $day,
            !$this->isIgnoreDayOfWeek($day),
            !$this->isHoliday($day)
        );
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
    public function setHolidays(array $holidays): BusinessDayPolicy
    {
        $this->holidays = [];
        foreach ($holidays as $holiday) {
            $this->holidays[$holiday->format($this->dateFormat)] = $holiday;
        }
        return $this;
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
     * @param AdditionalPolicyInterface $AdditionalPolicy
     * @return BusinessDayPolicy
     */
    public function setAdditionalPolicy(AdditionalPolicyInterface $AdditionalPolicy): BusinessDayPolicy
    {
        $this->AdditionalPolicy = $AdditionalPolicy;
        return $this;
    }
}
