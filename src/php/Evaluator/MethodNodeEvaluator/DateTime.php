<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\DateTime as DateTimeInterface;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class DateTime extends AbstractMethodHandler implements DateTimeInterface
{
    public function date($year, $month, $day)
    {
        return Carbon::createFromDate($year, $month, $day)->startOfDay();
    }
    public function dateValue($string)
    {
        return Carbon::parse($string);
    }
    public function day($datetime)
    {
        return Carbon::parse($datetime)->day;
    }
    public function edate($datetime, $months)
    {
        return Carbon::parse($datetime)->addMonths($months);
    }
    public function hour($datetime)
    {
        return Carbon::parse($datetime)->hour;
    }
    public function minute($datetime)
    {
        return Carbon::parse($datetime)->minute;
    }
    public function month($datetime)
    {
        return Carbon::parse($datetime)->month;
    }
    public function now()
    {
        return Carbon::now();
    }
    public function second($datetime)
    {
        return Carbon::parse($datetime)->second;
    }
    public function time($hours, $minutes, $seconds)
    {
        return new CarbonInterval(0, 0, 0, 0, $hours, $minutes, $seconds);
    }
    public function timeValue($string)
    {
        return CarbonInterval::createFromDateString($string);
    }
    public function today()
    {
        return Carbon::today();
    }
    public function weekday($date)
    {
        return Carbon::parse($date)->dayOfWeekIso;
    }
    public function year($date)
    {
        return Carbon::parse($date)->year;
    }
}
