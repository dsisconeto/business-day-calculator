<?php

namespace DSisconeto\BusinessDayCalculator;


interface HolidayInterval
{

    public function getStart(): \DateTime;

    public function getEnd(): \DateTime;
}
