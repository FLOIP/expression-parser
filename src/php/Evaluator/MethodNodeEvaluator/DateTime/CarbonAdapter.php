<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime;

use Carbon\Carbon;
use DateTime;
use Stringable;
use function method_exists;

class CarbonAdapter extends Carbon implements Stringable {
	
	private static $shouldDefer;

	private array $settings = [];
	
	public function settings(array $settings) {
		return $this->deferIfImplemented(__FUNCTION__, function (array $settings) {
			$this->settings = $settings;
			return $this;
		}, $settings);
	}

	private function deferIfImplemented($methodName, callable $implementation, array $args = []) {
		if (self::$shouldDefer === true) {
			return call_user_func_array([$this, "parent::$methodName"], [$args]);
		}
		if (method_exists(parent::class, $methodName)) {
			self::$shouldDefer = true;
			return $this->deferIfImplemented($methodName, $implementation, $args);
		}
		self::$shouldDefer = false;
		return $implementation($args);
	}

	public function add($unit, $value = 1, $overflow = null): DateTime|Carbon|CarbonAdapter {
		return (new Carbon($this))->add($unit, $value, $overflow);
	}

	public function sub($unit, $value = 1, $overflow = null): DateTime|Carbon|CarbonAdapter {
		return (new Carbon($this))->sub($unit, $value, $overflow);
	}

	public function __toString(): string {
		if (method_exists(parent::class, 'settings')) {
			return parent::__toString();
		}
		return $this->format($this->settings['toStringFormat']);
	}
}
