<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

use Carbon\Carbon;

interface DateTime extends EvaluatesMethods
{
    /**
     * Defines a new date value
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return Carbon
     */
    public function date($year, $month, $day);

    /**
     * Converts date stored in text to an actual date, using your organization's date format setting
     *
     * @param string $string
     * @return Carbon
     */
    public function dateValue($string);

    /**
     * Returns only the day of the month of a date (1 to 31)
     *
     * @param Carbon|string $datetime
     * @return int
     */
    public function day($datetime);

    /**
     * Moves a date by the given number of months
     *
     * @param Carbon|string $datetime
     * @param int $months
     * @return Carbon
     */
    public function edate($datetime, $months);

    /**
     * Returns only the hour of a datetime (0 to 23)
     *
     * @param Carbon|string $datetime
     * @return int
     */
    public function hour($datetime);

    /**
     * Returns only the minute of a datetime (0 to 59)
     *
     * @param Carbon|string $datetime
     * @return int
     */
    public function minute($datetime);

    /**
     * Returns only the month of a date (1 to 12)
     *
     * @param Carbon|string $datetime
     * @return int
     */
    public function month($datetime);

    /**
     * Returns the current date and time
     *
     * @return Carbon
     */
    public function now();

    /**
     * Returns only the second of a datetime (0 to 59)
     *
     * @param Carbon|string $datetime
     * @return int
     */
    public function second($datetime);

    /**
     * Defines a time value which can be used for time arithmetic
     *
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return Carbon
     */
    public function time($hours, $minutes, $seconds);

    /**
     * Converts time stored in text to an actual time
     *
     * @param string $string
     * @return Carbon
     */
    public function timeValue($string);

    /**
     * Returns the current date
     *
     * @return Carbon
     */
    public function today();

    /**
     * Returns the day of the week of a date (1 for Sunday to 7 for Saturday)
     *
     * @param Carbon|string $date
     * @return int
     */
    public function weekday($date);

    /**
     * Returns only the year of a date
     *
     * @param Carbon|string $date
     * @return int
     */
    public function year($date);
}
