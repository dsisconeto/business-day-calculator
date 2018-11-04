<?php

namespace DSisconeto\BusinessDayCalculator;

use DateTime;

interface AdditionalPolicyInterface
{
    public function isBusinessDay(DateTime $date, bool $isIgnoreDay, bool $isHoliday): bool;
}
