<?php
require_once __DIR__ . '/../vendor/autoload.php';

use DSisconeto\BusinessDayCalculator\BusinessDayPolicy;
use DSisconeto\BusinessDayCalculator\BusinessDaysCalculator;
use DSisconeto\BusinessDayCalculator\DayOfWeek;

$businessDayPolicy = new BusinessDayPolicy();
$businessDayCalculator = new BusinessDaysCalculator($businessDayPolicy);
$businessDayPolicy->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY])
    ->setHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

$startAt = new DateTime('2018-11-01');
$endAt = new DateTime('2018-11-30');
$datesWithAdditional = $businessDayCalculator->fromDateEnd($startAt, $endAt, true);
$dates = $businessDayCalculator->fromDateEnd($startAt, $endAt, false);

var_dump($datesWithAdditional);
var_dump($dates);
