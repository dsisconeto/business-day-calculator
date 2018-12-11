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
     * @param bool $extendable
     * @return BusinessDays
     */
    public function fromDays(DateTime $startAt, int $days, bool $extendable): BusinessDays
    {
        $dateEnd = (clone $startAt)->modify("+{$days} day");
        return $this->calculate($startAt, $dateEnd, $extendable);
    }

    /**
     * @param DateTime $startAt
     * @param DateTime $endAt
     * @param bool $extendable
     * @return BusinessDays
     */
    private function calculate(DateTime $startAt, DateTime $endAt, bool $extendable): BusinessDays
    {
        $dates = $this->fillDatePeriod($startAt, $endAt);

        if ($extendable) {
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
            $extendableDates = $this->calculateAdditionalDate($dates);
            $filteredDates = $this->filterDates($dates);
            $dates = array_merge($filteredDates, $extendableDates);
            if (count($extendableDates) === 0) {
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
        $extendableDates = [];
        foreach ($dates as $date) {
            if ($this->businessDayPolicy->isBusinessDay($date)) {
                continue;
            }
            $extendableDates[] = $this->nextAdditionalDate($extendableDates, $dates);
        }
        return $extendableDates;
    }

    /**
     * @param DateTime[] $extendableDates
     * @param DateTime[] $dates
     * @return DateTime
     */
    private function nextAdditionalDate(array $extendableDates, array $dates): DateTime
    {
        if (empty($extendableDates)) {
            return (clone $dates[count($dates) - 1])->modify('+1 day');
        }
        return (clone $extendableDates[count($extendableDates) - 1])->modify('+1 day');
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
     * @param bool $extendable
     * @return BusinessDays
     */
    public function fromInterval(DateTime $startAt, DateTime $endAt, bool $extendable): BusinessDays
    {
        return $this->calculate($startAt, $endAt, $extendable);
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
