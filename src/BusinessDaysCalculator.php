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
     * @var DateTime[]
     */
    private $dates = [];

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
     * @param DateTime $endAt
     * @param bool $additional
     * @return array
     */
    private function calculate(DateTime $startAt, DateTime $endAt, bool $additional): array
    {
        $this->dates = [];
        $this->fillDatePeriod($startAt, $endAt);
        if ($additional) {
            $this->calculateWithAdditional();
        } else {
            $this->calculateWithoutAdditional();
        }
        return $this->dates;
    }

    /**
     *
     */
    private function calculateWithoutAdditional()
    {
        $this->dates = $this->filterDates();
    }

    /**
     *
     */
    private function calculateWithAdditional()
    {
        while (true) {
            $additionalDates = $this->calculateAdditionalDate();
            $filteredDates = $this->filterDates();
            $this->dates = array_merge($filteredDates, $additionalDates);
            if (count($additionalDates) === 0) break;
        }
    }

    /**
     * @return array
     */
    private function calculateAdditionalDate(): array
    {
        $additionalDates = [];
        foreach ($this->dates as $date) {
            if ($this->businessDayPolicy->isBusinessDay($date)) continue;
            $additionalDates[] = $this->nextAdditionalDate($additionalDates);
        }
        return $additionalDates;
    }

    /**
     * @param DateTime[] $additionalDates
     * @return DateTime
     */
    private function nextAdditionalDate($additionalDates): DateTime
    {
        if (empty($additionalDates)) {
            return $this->getDateEnd()->modify('+1 day');
        }
        return (clone $additionalDates[count($additionalDates) - 1])->modify('+1 day');
    }

    /**
     * @return array
     */
    private function filterDates(): array
    {
        return array_filter($this->dates, function (DateTime $date) {
            return $this->businessDayPolicy->isBusinessDay($date);
        });
    }


    /**
     * @return DateTime
     */
    public function getDateEnd(): DateTime
    {
        return (clone $this->dates[count($this->dates) - 1]);
    }

    /**
     * @param DateTime $startAt
     * @param DateTime $endAt
     */
    private function fillDatePeriod(DateTime $startAt, DateTime $endAt)
    {
        $endAt = clone $endAt;
        $endAt->modify('+1 day');
        $period = new DatePeriod(
            $startAt,
            DateInterval::createFromDateString('1 day'),
            $endAt
        );
        foreach ($period as $date) {
            $this->dates[] = $date;
        }
    }
}
