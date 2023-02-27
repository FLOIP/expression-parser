<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\DateTime as DateTimeInterface;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime\CarbonAdapter;

class DateTime extends AbstractMethodHandler implements DateTimeInterface
{
    public function date($year, $month, $day): Carbon {
	    return Carbon::createFromDate($year, $month, $day)->startOfDay();
    }
	
	public function dateValue($string): Carbon {
		return Carbon::parse($string);
	}
	
	public function day($datetime): int {
		return Carbon::parse($datetime)->day;
	}
	
	public function edate($datetime, $months) {
		return Carbon::parse($datetime)->addMonths($months);
	}
	
	public function hour($datetime): int {
		return Carbon::parse($datetime)->hour;
	}
	
	public function minute($datetime): int {
		return Carbon::parse($datetime)->minute;
	}
	
	public function month($datetime): int {
		return Carbon::parse($datetime)->month;
	}
	
	public function now(): Carbon {
		return Carbon::now();
	}
	
	public function second($datetime): int {
		return Carbon::parse($datetime)->second;
	}
	
	public function time($hours, $minutes, $seconds): CarbonInterval {
		return new CarbonInterval(0, 0, 0, 0, $hours, $minutes, $seconds);
	}
	
	public function timeValue($string): CarbonInterval {
		$matches = [];
		if (\preg_match(DateTimeInterface::TIME_REGEX, $string, $matches)) {
			return CarbonInterval::fromString("{$matches[1]}h {$matches[2]}m");
		}
		return CarbonInterval::fromString($string);
	}
	
	public function today(): Carbon {
		return CarbonAdapter::today()->settings(['toStringFormat' => 'Y-m-d']);
	}
	
	public function weekday($date): int {
		return Carbon::parse($date)->dayOfWeekIso;
	}
	
	public function year($date): int {
		return Carbon::parse($date)->year;
	}
	
	public function between($date, $start, $end): bool {
		return Carbon::parse($date)->between(Carbon::parse($start), Carbon::parse($end));
	}
}
