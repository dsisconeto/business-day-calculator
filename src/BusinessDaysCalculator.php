<?php

namespace DSisconeto\BusinessDayCalculator;

use \DateTime;
use \DateInterval;
use \DatePeriod;

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
     * @param bool $additional
     * @return array
     */
    public function fromDays(DateTime $startAt, int $days, bool $additional = false): array
    {
        $dateEnd = (clone $startAt)->modify("+{$days} day");
        return $this->calculate($startAt, $dateEnd, $additional);
    }


    /**
     * @param DateTime $startAt
     * @param DateTime $endAt
     * @param bool $additional
     * @return array
     */
    public function fromDateEnd(DateTime $startAt, DateTime $endAt, bool $additional = false): array
    {
        return $this->calculate($startAt, $endAt, $additional);
    }

    /**
     * @param DateTime $startAt
     * @return DateTime
     */
    public function nextBusinessDay(DateTime $startAt): DateTime
    {
        $endAt = (clone $startAt)->modify('+1 day');
        $dates = $this->calculate($startAt, $endAt, true);
        return $dates[0];
    }


    /**
     * @param DateTime $startAt
     * @param DateTime $endAt
     * @param bool $additional
     * @return array
     */
    private function calculate(DateTime $startAt, DateTime $endAt, bool $additional): array
    {
        $dates = $this->fillDatePeriod($startAt, $endAt);
        if ($additional) {
            $dates = $this->calculateWithAdditional($dates);
        } else {
            $dates = $this->calculateWithoutAdditional($dates);
        }
        return $dates;
    }


    /**
     * @param DateTime[] $dates
     * @return DateTime[]
     */
    private function calculateWithoutAdditional(array $dates): array
    {
        return $this->filterDates($dates);
    }


    /**
     * @param DateTime[] $dates
     * @return DateTime[]
     */
    private function calculateWithAdditional(array $dates): array
    {
        while (true) {
            $additionalDates = $this->calculateAdditionalDate($dates);
            $filteredDates = $this->filterDates($dates);
            $dates = array_merge($filteredDates, $additionalDates);
            if (count($additionalDates) === 0) {
                break;
            };
        }
        return $dates;
    }

    /**
     * @param  DateTime[] $dates
     * @return DateTime[]
     */
    private function calculateAdditionalDate(array $dates): array
    {
        $additionalDates = [];
        foreach ($dates as $date) {
            if ($this->businessDayPolicy->isBusinessDay($date)) {
                continue;
            }
            $additionalDates[] = $this->nextAdditionalDate($additionalDates, $dates);
        }
        return $additionalDates;
    }

    /**
     * @param DateTime[] $additionalDates
     * @param DateTime[] $dates
     * @return DateTime
     */
    private function nextAdditionalDate(array $additionalDates, array $dates): DateTime
    {
        if (empty($additionalDates)) {
            return (clone $dates[count($dates) - 1])->modify('+1 day');
        }
        return (clone $additionalDates[count($additionalDates) - 1])->modify('+1 day');
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
     * @param DateTime $startAt
     * @param DateTime $endAt
     * @return array
     */
    private function fillDatePeriod(DateTime $startAt, DateTime $endAt): array
    {
        $endAt = clone $endAt;
        $endAt->modify('+1 day');
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
}
