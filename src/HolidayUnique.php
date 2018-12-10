<?php

namespace DSisconeto\BusinessDayCalculator;


use DateTime;

interface HolidayUnique
{
    public function getDate(): DateTime;
}
