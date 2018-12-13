<?php

namespace DSisconeto\BusinessDayCalculator;

interface HolidayIntervalInterface
{
    public function getStart(): \DateTime;

    public function getEnd(): \DateTime;
}
