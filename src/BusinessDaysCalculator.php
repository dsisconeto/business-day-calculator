<?php

namespace DSisconeto\BusinessDayCalculator;

use DateInterval;
use DatePeriod;
use DateTime;

/**
 * Class BusinessDaysCalculator
 * @package DSisconeto\BusinessDayCalculator
 */
class BusinessDaysCalculator
{
    /**
     * @var BusinessDayPolicyInterface
     */
    private $businessDayPolicy;


    /**
     * BusinessDaysCalculator constructor.
     * @param BusinessDayPolicyInterface $businessDayPolicy
     */
    public function __construct(BusinessDayPolicyInterface $businessDayPolicy)
    {
        $this->businessDayPolicy = $businessDayPolicy;
    }

    /**
     * @param DateTime $startAt
     * @param int $days
     * @param bool $extendDate
     * @return BusinessDays
     */
    public function fromDays(DateTime $startAt, int $days, bool $extendDate = false): BusinessDays
    {
        $dateEnd = (clone $startAt)->modify("+{$days} day");
        return $this->calculate($startAt, $dateEnd, $extendDate);
    }

    /**
     * @param DateTime $startAt
     * @param DateTime $endAt
     * @param bool $extendDate
     * @return BusinessDays
     */
    private function calculate(DateTime $startAt, DateTime $endAt, bool $extendDate): BusinessDays
    {
        $dates = $this->fillDatePeriod($startAt, $endAt);

        if ($extendDate) {
            $dates = $this->calculateExtendingDate($dates);
        } else {
            $dates = $this->calculateWithoutExtendingTheDate($dates);
        }

        return new BusinessDays($dates);
    }

    /**
     * @param DateTime $startAt
     * @param DateTime $endAt
     * @return array
     */
    private function fillDatePeriod(DateTime $startAt, DateTime $endAt): array
    {
        $endAt = (clone $endAt)->modify('+1 day');
        $period = new DatePeriod(
            $startAt,
            DateInterval::createFromDateString('1 day'),
            $endAt
        );
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date;
        }
        return $dates;
    }

    /**
     * @param DateTime[] $dates
     * @return DateTime[]
     */
    private function calculateExtendingDate(array $dates): array
    {
        while (true) {
            $extendDateDates = $this->calculateAdditionalDate($dates);
            $filteredDates = $this->filterDates($dates);
            $dates = array_merge($filteredDates, $extendDateDates);
            if (count($extendDateDates) === 0) {
                break;
            }
        }
        return $dates;
    }

    /**
     * @param  DateTime[] $dates
     * @return DateTime[]
     */
    private function calculateAdditionalDate(array $dates): array
    {
        $extendDateDates = [];
        foreach ($dates as $date) {
            if ($this->businessDayPolicy->isBusinessDay($date)) {
                continue;
            }
            $extendDateDates[] = $this->nextAdditionalDate($extendDateDates, $dates);
        }
        return $extendDateDates;
    }

    /**
     * @param DateTime[] $extendDateDates
     * @param DateTime[] $dates
     * @return DateTime
     */
    private function nextAdditionalDate(array $extendDateDates, array $dates): DateTime
    {
        if (empty($extendDateDates)) {
            return (clone $dates[count($dates) - 1])->modify('+1 day');
        }
        return (clone $extendDateDates[count($extendDateDates) - 1])->modify('+1 day');
    }

    /**
     * @param DateTime[] $dates
     * @return array
     */
    private function filterDates(array $dates): array
    {
        return array_filter($dates, function (DateTime $date) {
            return $this->businessDayPolicy->isBusinessDay($date);
        });
    }

    /**
     * @param DateTime[] $dates
     * @return DateTime[]
     */
    private function calculateWithoutExtendingTheDate(array $dates): array
    {
        return $this->filterDates($dates);
    }

    /**
     * @param DateTime $startAt
     * @param DateTime $endAt
     * @param bool $extendDate
     * @return BusinessDays
     */
    public function fromInterval(DateTime $startAt, DateTime $endAt, bool $extendDate): BusinessDays
    {
        return $this->calculate($startAt, $endAt, $extendDate);
    }

    /**
     * @param DateTime $startAt
     * @return DateTime
     */
    public function nextBusinessDay(DateTime $startAt): DateTime
    {
        $endAt = (clone $startAt)->modify('+1 day');
        $dates = $this->calculate($startAt, $endAt, true);
        return $dates->getDateStart();
    }
}
