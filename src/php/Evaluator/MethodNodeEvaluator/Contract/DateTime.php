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
     * @return Carbon
     */
    public function date($year, $month, $day): Carbon;

    /**
     * Converts date stored in text to an actual date, using your organization's date format setting
     *
     * @param string $string
     * @return Carbon
     */
	public function dateValue($string): Carbon;

    /**
     * Returns only the day of the month of a date (1 to 31)
     *
     * @param Carbon|string $datetime
     * @return int
     */
	public function day($datetime): int;

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
	public function hour($datetime): int;

    /**
     * Returns only the minute of a datetime (0 to 59)
     *
     * @param Carbon|string $datetime
     * @return int
     */
	public function minute($datetime): int;

    /**
     * Returns only the month of a date (1 to 12)
     *
     * @param Carbon|string $datetime
     * @return int
     */
	public function month($datetime): int;

    /**
     * Returns the current date and time
     *
     * @return Carbon
     */
	public function now(): Carbon;

    /**
     * Returns only the second of a datetime (0 to 59)
     *
     * @param Carbon|string $datetime
     * @return int
     */
	public function second($datetime): int;

    /**
     * Defines a time value which can be used for time arithmetic
     *
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return CarbonInterval
     */
	public function time($hours, $minutes, $seconds): CarbonInterval;

    /**
     * Converts time stored in text to an actual time
     *
     * @param string $string
     * @return CarbonInterval
     */
	public function timeValue($string): CarbonInterval;

    /**
     * Returns the current date
     *
     * @return Carbon
     */
	public function today(): Carbon;

    /**
     * Returns the day of the week of a date (1 for Sunday to 7 for Saturday)
     *
     * @param Carbon|string $date
     * @return int
     */
	public function weekday($date): int;

    /**
     * Returns only the year of a date
     *
     * @param Carbon|string $date
     * @return int
     */
	public function year($date): int;

    /**
     * Determine if a date falls between $start and $end, inclusive.
     *
     * @param Carbon|string $date
     * @param Carbon|string $start
     * @param Carbon|string $end
     * @return bool
     */
	public function between($date, $start, $end): bool;
}
