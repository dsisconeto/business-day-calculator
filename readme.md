
###Install via composer
````bash
composer require dsisconeto/business-day-calculator
````

### How to use
````php
use DSisconeto\BusinessDayCalculator\BusinessDayPolicy;
use DSisconeto\BusinessDayCalculator\BusinessDaysCalculator;
use DSisconeto\BusinessDayCalculator\DayOfWeek;

$businessDayPolicy = new BusinessDayPolicy();
$businessDayCalculator = new BusinessDaysCalculator($businessDayPolicy);
$businessDayPolicy->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY])
    ->addHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

$startAt = new DateTime('2018-11-01');
$endAt = new DateTime('2018-11-30');
$datesWithAdditional = $businessDayCalculator->fromDateEnd($startAt, $endAt, true);
$dates = $businessDayCalculator->fromDateEnd($startAt, $endAt, false);

var_dump($datesWithAdditional);
var_dump($dates);
````
````php
use DSisconeto\BusinessDayCalculator\BusinessDayPolicy;
use DSisconeto\BusinessDayCalculator\BusinessDaysCalculator;
use DSisconeto\BusinessDayCalculator\DayOfWeek;

$businessDayPolicy = new BusinessDayPolicy();
$businessDayCalculator = new BusinessDaysCalculator($businessDayPolicy);
$businessDayPolicy->setIgnoreDaysOfWeek([DayOfWeek::SUNDAY, DayOfWeek::SATURDAY])
    ->addHolidays([new DateTime('2018-11-02'), new DateTime('2018-11-15')]);

$startAt = new DateTime('2018-11-01');
$datesWithAdditional = $businessDayCalculator->fromDays($startAt, 30, true);
$dates = $businessDayCalculator->fromDays($startAt, 30, false);
var_dump($datesWithAdditional);
var_dump($dates);

````
