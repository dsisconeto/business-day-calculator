<?php
namespace DSisconeto\BusinessDayCalculator;

use DateTime;

interface HolidayUniqueInterface
{
    public function getDate(): DateTime;
}
