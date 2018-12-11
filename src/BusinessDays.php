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
        [array_key_first($dates) => $this->start, array_key_last($dates) => $this->end] = $dates;

        $this->dates = array_values($dates);
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
