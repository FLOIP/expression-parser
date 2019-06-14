<?php

namespace Viamo\Floip\Evaluator\MethodEvaluator;

use Viamo\Floip\Evaluator\MethodEvaluator\Contract\DateTime as DateTimeInterface;
use Carbon\Carbon;

class DateTime extends AbstractMethodHandler implements DateTimeInterface
{
    public function date($year, $month, $day)
    {
        return Carbon::createFromDate($year, $month, $day);
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
        return Carbon::createFromTime($hours, $minutes, $seconds);
    }
    public function timeValue($string)
    {
        return Carbon::parse($string);
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
