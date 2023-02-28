<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\DateTime as DateTimeInterface;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime\CarbonAdapter;
use function preg_match;

class DateTime extends AbstractMethodHandler implements DateTimeInterface
{
    public function date(int $year, int $month, int $day): Carbon {
        return Carbon::createFromDate($year, $month, $day)->startOfDay();
    }
    
    public function dateValue(string $string): Carbon {
        return Carbon::parse($string);
    }
    
    public function day(Carbon|string $datetime): int {
        return Carbon::parse($datetime)->day;
    }
    
    public function edate(Carbon|string $datetime, int $months): Carbon {
        return Carbon::parse($datetime)->addMonths($months);
    }
    
    public function hour(Carbon|string $datetime): int {
        return Carbon::parse($datetime)->hour;
    }
    
    public function minute(Carbon|string $datetime): int {
        return Carbon::parse($datetime)->minute;
    }
    
    public function month(Carbon|string $datetime): int {
        return Carbon::parse($datetime)->month;
    }
    
    public function now(): Carbon {
        return Carbon::now();
    }
    
    public function second(Carbon|string $datetime): int {
        return Carbon::parse($datetime)->second;
    }
    
    public function time(int $hours, int $minutes, int $seconds): CarbonInterval {
        return new CarbonInterval(0, 0, 0, 0, $hours, $minutes, $seconds);
    }
    
    public function timeValue(string $string): CarbonInterval {
        $matches = [];
        if (preg_match(DateTimeInterface::TIME_REGEX, $string, $matches)) {
            return CarbonInterval::fromString("{$matches[1]}h {$matches[2]}m");
        }
        return CarbonInterval::fromString($string);
    }
    
    public function today(): Carbon {
        return CarbonAdapter::today()->settings(['toStringFormat' => 'Y-m-d']);
    }
    
    public function weekday(Carbon|string $date): int {
        return Carbon::parse($date)->dayOfWeekIso;
    }
    
    public function year(Carbon|string $date): int {
        return Carbon::parse($date)->year;
    }
    
    public function between(Carbon|string $date,Carbon|string $start, Carbon|string $end): bool {
        return Carbon::parse($date)->between(Carbon::parse($start), Carbon::parse($end));
    }
}
