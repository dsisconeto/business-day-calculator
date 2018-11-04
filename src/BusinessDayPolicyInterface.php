<?php

namespace DSisconeto\BusinessDayCalculator;

use DateTime;

/**
 * Interface BusinessDayPolicyInterface
 * @package DSisconeto\BusinessDayCalculator
 */
interface BusinessDayPolicyInterface
{
    /**
     * @param DateTime $day
     * @return bool
     */
    public function isBusinessDay(DateTime $day): bool;
}
