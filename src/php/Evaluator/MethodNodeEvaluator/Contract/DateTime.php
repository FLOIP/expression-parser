<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

use Carbon\Carbon;
use Carbon\CarbonInterval;

interface DateTime extends EvaluatesMethods
{
    const TIME_REGEX = "/^([0-9]{1,2}):([0-9]{2})$/";
    const DATE_INTERVAL_REGEX = "/^([0-9]+)\s\w+$/i";

    /**
     * Defines a new date value
     *
     * @param int $year
     * @param int $month
     * @param int $day
     */
    public function date(int $year, int $month, int $day): Carbon;

    /**
     * Converts date stored in text to an actual date, using your organization's date format setting
     *
     * @param string $string
     */
    public function dateValue(string $string): Carbon;

    /**
     * Returns only the day of the month of a date (1 to 31)
     */
    public function day(Carbon|string $datetime): int;

    /**
     * Moves a date by the given number of months
     *
     *
     */
    public function edate(Carbon|string $datetime, int $months): Carbon;

    /**
     * Returns only the hour of a datetime (0 to 23)
     */
    public function hour(Carbon|string $datetime): int;

    /**
     * Returns only the minute of a datetime (0 to 59)
     */
    public function minute(Carbon|string $datetime): int;

    /**
     * Returns only the month of a date (1 to 12)
     */
    public function month(Carbon|string $datetime): int;

    /**
     * Returns the current date and time
     */
    public function now(): Carbon;

    /**
     * Returns only the second of a datetime (0 to 59)
     */
    public function second(Carbon|string $datetime): int;

    /**
     * Defines a time value which can be used for time arithmetic
     *
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     */
    public function time(int $hours, int $minutes, int $seconds): CarbonInterval;

    /**
     * Converts time stored in text to an actual time
     *
     * @param string $string
     */
    public function timeValue(string $string): CarbonInterval;

    /**
     * Returns the current date
     */
    public function today(): Carbon;

    /**
     * Returns the day of the week of a date (1 for Sunday to 7 for Saturday)
     */
    public function weekday(Carbon|string $date): int;

    /**
     * Returns only the year of a date
     */
    public function year(Carbon|string $date): int;

    /**
     * Determine if a date falls between $start and $end, inclusive.
     */
    public function between(Carbon|string $date, Carbon|string $start, Carbon|string $end): bool;
}
