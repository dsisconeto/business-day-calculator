<?php

namespace DSisconeto\BusinessDayCalculator;

use DateTime;

class BusinessDays
{
    /**
     * @var array|DateTime[]
     */
    private $dates;
    /**
     * @var DateTime
     */
    private $start;
    /**
     * @var DateTime
     */
    private $end;

    /**
     * BusinessDays constructor.
     * @param DateTime[] $dates
     */
    public function __construct(array $dates)
    {
        $this->dates = array_values($dates);
        [0 => $this->start, count($this->dates) - 1 => $this->end] = $this->dates;
    }

    public function getDateEnd(): DateTime
    {
        return clone $this->end;
    }

    public function getDateStart(): DateTime
    {
        return clone $this->start;
    }

    public function count(): int
    {

        return count($this->dates);
    }

    public function getDates(): array
    {
        return $this->dates;
    }
}
