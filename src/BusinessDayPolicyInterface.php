<?php
/**
 * Created by PhpStorm.
 * User: dsisconeto
 * Date: 02/11/2018
 * Time: 21:09
 */

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
