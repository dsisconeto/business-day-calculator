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
    private $AdditionalPolicy;

    /**
     * @param DateTime $day
     * @return bool
     */
    public function isBusinessDay(DateTime $day): bool
    {
        if ($this->AdditionalPolicy === null) {
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
     * @param array[] $holidays
     * @return BusinessDayPolicy
     */
    public function setHolidays(array $holidays): BusinessDayPolicy
    {
        $this->holidays = [];

        foreach ($holidays as $holiday) {
            if ($holiday instanceof HolidayIntervalInterface) {
                $this->setInterval($holiday);
                continue;
            }

            if ($holiday instanceof HolidayUniqueInterface) {
                $this->setUnique($holiday);
                continue;
            }

            if ($holiday instanceof \DateTimeInterface) {
                $this->setHoliday($holiday);
                continue;
            }
        }
        return $this;
    }

    public function setInterval(HolidayIntervalInterface $holidayInterval): void
    {
        $period = new \DatePeriod(
            $holidayInterval->getStart(),
            \DateInterval::createFromDateString('1 day'),
            $holidayInterval->getEnd()->modify('+1 day')
        );

        /**
         * @var DateTime $date
         */
        foreach ($period as $date) {
            $this->setHoliday($date);
        }
    }

    private function setHoliday(DateTime $date): void
    {
        $this->holidays[$date->format($this->dateFormat)] = $date->format($this->dateFormat);
    }

    private function setUnique(HolidayUniqueInterface $holidayUnique): void
    {
        $this->setHoliday($holidayUnique->getDate());
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
     * @param AdditionalPolicyInterface $additionalPolicy
     * @return BusinessDayPolicy
     */
    public function setAdditionalPolicy(AdditionalPolicyInterface $additionalPolicy): BusinessDayPolicy
    {
        $this->AdditionalPolicy = $additionalPolicy;
        return $this;
    }
}
